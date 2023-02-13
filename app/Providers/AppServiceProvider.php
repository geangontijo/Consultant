<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Services\HttpClient\HttpClientStripe;
use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
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
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!App::runningInConsole() || !App::runningUnitTests()) {
            class_alias(PersonalAccessToken::class, \Laravel\Sanctum\PersonalAccessToken::class);
        }

        Http::macro('stripe', fn() => Http::withMiddleware(
            Middleware::mapRequest(function (RequestInterface $request) {
                return $request->withHeader(
                    'Authorization',
                    'Basic '.base64_encode(
                        'sk_test_51MaTmCJtc69aInenGCb74m8UFq1uMXxeh6WjLbEQmJlkem4DczEkibt9P7M5UmOkTm4cgzRpJU90PKGvgAcN53cY008jPhW3yp'
                    ).':'
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

        Sanctum::$personalAccessTokenModel = PersonalAccessToken::class;
        DB::listen(function (QueryExecuted $query) {
            file_put_contents('php://stdout', "[SQL] {$query->sql} \n");
        });
    }
}
