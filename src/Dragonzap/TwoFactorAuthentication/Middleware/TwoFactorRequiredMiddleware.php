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

        $two_factor_code = TwoFactorAuthentication::generateCode();
        $two_factor_code->send();
        return redirect()->route('dragonzap.two_factor_enter_code');
        
      
    }

}
