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

        return redirect()->route('dragonzap.two_factor_enter_code');
        
        return $next($request);
    }

}
