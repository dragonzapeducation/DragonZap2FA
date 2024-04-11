<?php

namespace Dragonzap\TwoFactorAuthentication;
use Illuminate\Support\Facades\Session;
class TwoFactorAuthentication   
{
    public static function generateCode() : TwoFactorCode
    {
        // Secure random
        $random_code = random_int(100000, 999999);
        Session::put('two_factor_code', $random_code);
        Session::put('two_factor_code_time', now());
        Session::save();
        return new TwoFactorCode($random_code, now());
    }

    /**
     * Sets the return URL for the two factor authentication process. I.e the URL to return to after the user has authenticated
     */
    public static function setReturnUrl(string $url) : void
    {
        Session::put('two_factor_return_url', $url);
        Session::save();
    }
    /**
     * Gets the return URL for the two factor authentication process
     */
    public static function getReturnUrl() : string
    {
        return Session::get('two_factor_return_url');
    }

    /**
     * Releases the two factor authentication requirement for the session, after some time
     * this will expire and authentication will be required again.
     * @return bool
     */
    public static function releaseAuthRequirement() : void
    {
        self::clearCode();
        Session::put('two_factor_authenticated', true);
        Session::put('two_factor_authenticated_time', now());
        Session::save();
    }

    public static function isAuthenticationRequired()
    {
        if (!config('dragonzap_2factor.enabled'))
        {
            return false;
        }

        if (!Session::has('two_factor_authenticated') || !Session::get('two_factor_authenticated'))
        {
            return true;
        }


        $authenticated_time = Session::get('two_factor_authenticated_time');
        return $authenticated_time->diffInMinutes(now()) >= config('dragonzap_2factor.authentication.expires_in_minutes');
    }

    public static function clearCode()
    {
        Session::forget('two_factor_code');
        Session::forget('two_factor_code_time');
        Session::save();
    }


    public static function getGeneratedCode() : TwoFactorCode|null
    {
        if (!Session::has('two_factor_code'))
        {
            return null;
        }

        return new TwoFactorCode(Session::get('two_factor_code'), Session::get('two_factor_code_time'));
    }

    public static function hasExistingCode() : bool
    {
        $generated_code = self::getGeneratedCode();
        return $generated_code !== null && $generated_code->isValid();
    }
}

