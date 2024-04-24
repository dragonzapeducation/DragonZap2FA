<?php

namespace Dragonzap\TwoFactorAuthentication;

use Dragonzap\TwoFactorAuthentication\Exceptions\InvalidAuthenticationTypeException;
use Dragonzap\TwoFactorAuthentication\Models\TwoFactorTotp;
use Illuminate\Support\Facades\Session;

class TwoFactorAuthentication
{

    private static $handlerInstance = null;

    public static function getHandlerInstance()
    {
        if (self::$handlerInstance == null) {
            self::$handlerInstance = new (config('dragonzap_2factor.authentication.handler.class'))();
        }

        return self::$handlerInstance;
    }

    public static function setAuthenticatingUser($user) : void
    {
        self::getHandlerInstance()->setAuthenticatingUser($user);
    }

    public static function generateCode(): TwoFactorCode
    {
        // Secure random
        return self::getHandlerInstance()->generateCode();
    }

    /**
     * Sets the return URL for the two factor authentication process. I.e the URL to return to after the user has authenticated
     */
    public static function setReturnUrl(string $url): void
    {
        self::getHandlerInstance()->setReturnUrl($url);
    }
    /**
     * Gets the return URL for the two factor authentication process
     */
    public static function getReturnUrl(): string
    {
        return self::getHandlerInstance()->getReturnUrl();

    }

    /**
     * Releases the two factor authentication requirement for the session, after some time
     * this will expire and authentication will be required again.
     * @return bool
     */
    public static function releaseAuthRequirement(): void
    {
        self::getHandlerInstance()->releaseAuthRequirement();
    }

    public static function validateAuthenticationType($type)
    {
        self::getHandlerInstance()->validateAuthenticationType($type);
    }

    /**
     * Checks if the user needs to authenticate with two factor authentication
     * @param string $type This is the type of authentication required. 'if-enabled' means that the user must authenticate if two factor authentication is enabled, 'always' means that the user must always authenticate with two factor authentication regardless of the setting.
     * @throws DragonZap\TwoFactorAuthentication\Exceptions\InvalidAuthenticationTypeException if the type is invalid or not recognized
     */
    public static function isAuthenticationRequired($type = 'if-enabled'): bool
    {
        return self::getHandlerInstance()->isAuthenticationRequired($type);
    }

    public static function clearCode()
    {
        self::getHandlerInstance()->clearCode();
    }


    public static function getGeneratedCode(): TwoFactorCode|null
    {
        return self::getHandlerInstance()->getGeneratedCode();
    }

    public static function hasExistingCode(): bool
    {
        return self::getHandlerInstance()->hasExistingCode();
    }

    public static function authenticationCompleted() : void
    {
        self::getHandlerInstance()->authenticationCompleted();
    }

    public static function validateTwoFactorType($type)
    {
        if ($type != 'otp' && $type != 'totp') {
            throw new InvalidAuthenticationTypeException('Invalid two factor type');
        }
    }
    public static function updateAuthenticationTypeForUser($user, $type)
    {
        self::validateTwoFactorType($type);
        $user->two_factor_enabled = true;
        $user->two_factor_type = $type;
        $user->save();
        
        // We will mark authentication as completed so they dont get locked out right away
        self::authenticationCompleted();
    }

    /**
     * Gets the two factor type for the given user.
     */
    public static function getTwoFactorTypeForUser($user)
    {
        return $user->two_factor_type;
    }


    
    /**
     * Generates a TOTP for the given user
     * @param $user The user to generate the TOTP for
     * @param $friendly_name The friendly name for the TOTP
     * @param $delete_others If true, all other TOTP's for the user will be deleted
     */
    public static function generateTotpForUser($user, $friendly_name=NULL, $delete_others=false) : TwoFactorTotp
    {
        if ($delete_others)
        {
            TwoFactorTotp::forUser($user)->delete();
        }

        return TwoFactorTotp::generateTotp($user, $friendly_name);
    }

    /**
     * Gets all the TOTP registered authenticators for the given user.
     */
    public static function getTotpsForUser($user)
    {
        return TwoFactorTotp::forUser($user);
    }   

}

