<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Services\HttpClient\HttpClientStripe;
use GuzzleHttp\Middleware;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Manager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Penance316\Validators\IsoDateValidator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!App::isProduction()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!App::runningInConsole() && !App::runningUnitTests()) {
            class_alias(PersonalAccessToken::class, \Laravel\Sanctum\PersonalAccessToken::class);
        }

        Http::macro('stripe', fn () => Http::withMiddleware(
            Middleware::mapRequest(function (RequestInterface $request) {
                return $request->withHeader(
                    'Authorization',
                    'Basic ' . base64_encode(
                        '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'
                    ) . ':'
                );
            })
        )->withMiddleware(
            Middleware::mapResponse(function (ResponseInterface $response) {
                $code = $response->getStatusCode();
                $contents = $response->getBody()->getContents();
                if (!Str::isJson($contents)) {
                    throw new \DomainException('Stripe API response is not JSON.');
                }
                $contents = json_decode($contents, true);

                if ($code !== 200 || array_key_exists('errors', $contents)
                    || array_key_exists(
                        'error',
                        $contents
                    )) {
                    throw new \DomainException(
                        Arr::get(
                            $contents,
                            'errors.0.message',
                            Arr::get($contents, 'error.message', 'Stripe API error.')
                        )
                    );
                }

                return $response;
            })
        )->baseUrl('https://api.stripe.com/v1/')->withOptions([
            'verify' => App::isProduction()
        ])->asForm());

        Validator::extend('iso_date', IsoDateValidator::class . '@validateIsoDate');

        Sanctum::$personalAccessTokenModel = PersonalAccessToken::class;
        DB::listen(function (QueryExecuted $query) {
            file_put_contents('php://stdout', "[SQL] {$query->sql} \n");
        });
    }
}
