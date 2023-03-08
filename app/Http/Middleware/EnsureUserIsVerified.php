<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureUserIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $redirectToRoute
     * @return Response|RedirectResponse|null
     */
    public function handle(Request $request, Closure $next, string $redirectToRoute = null): mixed
    {
        /** @var User $user */
        $user = $request->user();
        if (!$user || !$user->verified_at) {
            return $request->expectsJson()
                ? abort(403, 'Your user is not verified.')
                : Redirect::route($redirectToRoute ?: 'home')->with('error', trans('auth.verify.failed'));
        }

        return $next($request);
    }
}
