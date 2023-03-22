<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use App\Models\Professional;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use DomainException;
use GuzzleHttp\Middleware;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Penance316\Validators\IsoDateValidator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use function array_key_exists;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!App::isProduction()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!App::runningInConsole() && !App::runningUnitTests()) {
            class_alias(PersonalAccessToken::class, \Laravel\Sanctum\PersonalAccessToken::class);
        }

        Http::macro('stripe', fn () => Http::withMiddleware(
            Middleware::mapRequest(static function (RequestInterface $request) {
                return $request->withHeader(
                    'Authorization',
                    'Basic ' . base64_encode(
                        env('STRIPE_SECRET')
                    ) . ':'
                );
            })
        )->withMiddleware(
            Middleware::mapResponse(static function (ResponseInterface $response) {
                $code = $response->getStatusCode();
                $contents = $response->getBody()->getContents();
                if (!Str::isJson($contents)) {
                    throw new DomainException('Stripe API response is not JSON.');
                }
                $contents = json_decode($contents, true);

                if (200 !== $code || array_key_exists('errors', $contents)
                    || array_key_exists(
                        'error',
                        $contents
                    )) {
                    throw new DomainException(Arr::get($contents, 'errors.0.message',
                        Arr::get($contents, 'error.message', 'Stripe API error.')));
                }

                return $response;
            })
        )->baseUrl('https://api.stripe.com/v1/')->withOptions([
            'verify' => App::isProduction(),
        ])->asForm());

        Validator::extend('iso_date', IsoDateValidator::class . '@validateIsoDate');

        Lang::macro('attribute', function (string $key): string|null {
            $attribute = Arr::get(trans('validation.attributes'), preg_replace('/\.\d\./', '.*.', $key));

            return $attribute ?? $key;
        });
        $this->app->instance(Professional::class, new Professional());
        $this->app->bind(TokenRepositoryInterface::class, function () {
            $key = $this->app['config']['app.key'];

            if (str_starts_with($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            $defaultPasswordBroker = $this->app['config']['auth.defaults.passwords'];
            $config = $this->app['config']["auth.passwords.{$defaultPasswordBroker}"];
            # TODO: connection be mongodb
            return new DatabaseTokenRepository(
                $this->app['db']->connection(),
                $this->app['hash'],
                $config['table'],
                $key,
                $config['expire'],
                $config['throttle'] ?? 0
            );
        });

        Sanctum::$personalAccessTokenModel = PersonalAccessToken::class;
        DB::listen(static function (QueryExecuted $query): void {
            file_put_contents('php://stdout', "[SQL] {$query->sql} \n");
        });
    }
}
