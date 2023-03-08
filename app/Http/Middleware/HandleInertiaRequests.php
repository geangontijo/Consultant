<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy())->toArray(), [
                    'location' => $request->url(),
                ]);
            },
        ]);
    }

    public function resolveValidationErrors(Request $request): object
    {
        if (!$request->hasSession() || !$request->session()->has('errors')) {
            return (object)[];
        }

        return (object)collect($request->session()->get('errors')->getBags())->map(
            fn (MessageBag $bag) => (object)collect($bag->messages())->reduce(
                // TODO: Criar um agrupamento dos erros.
                fn (array $total, array $item, string $key) => array_merge(
                    $total,
                    [
                        $key => [
                            'messages' => $item,
                            'name' => Str::of(Arr::get(trans("validation.attributes"), preg_replace('/\.\d\./', '.*.', $key)))->replace('_', ' ')->ucfirst()->value()
                        ]
                    ]
                ),
                []
            )
        )->pipe(function ($bags) use ($request) {
            if ($bags->has('default') && $request->header('x-inertia-error-bag')) {
                return [$request->header('x-inertia-error-bag') => $bags->get('default')];
            }

            if ($bags->has('default')) {
                return $bags->get('default');
            }

            return $bags->toArray();
        });
    }
}
