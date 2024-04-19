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
   
     public function handle($request, Closure $next, $type = null)
     {
         if (!$type) {
             $type = 'if-enabled';
         }
     
         if (!TwoFactorAuthentication::isAuthenticationRequired($type)) {
             return $next($request);
         }
     
         $parameters = [];
         $urlWithParameters = $request->fullUrlWithQuery($parameters);
     
         TwoFactorAuthentication::setReturnUrl($urlWithParameters);
         
         return redirect()->route('dragonzap.two_factor_generate_code');
     }
     
    

}
