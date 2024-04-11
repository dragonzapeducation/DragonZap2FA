<?php

namespace Dragonzap\TwoFactorAuthentication\Middleware;

use Closure;

class TwoFactorRequiredMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user->two_factor_enabled)
        {
            return redirect()->route('two-factor.enable');
        }
        return $next($request);
    }

}
