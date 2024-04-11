<?php

namespace Dragonzap\TwoFactorAuthentication\Middleware;

use Closure;
use Dragonzap\TwoFactorAuthentication\TwoFactorAuthentication;

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
        if (config('dragonzap_2factor.enabled') == false) {
            return $next($request);
        }


        return redirect()->route('dragonzap.two_factor_generate_code');

    }

}
