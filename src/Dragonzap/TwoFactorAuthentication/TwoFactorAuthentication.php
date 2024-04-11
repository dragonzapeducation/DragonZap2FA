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

