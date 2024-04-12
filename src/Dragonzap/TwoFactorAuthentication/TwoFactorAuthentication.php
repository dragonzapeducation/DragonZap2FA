<?php

namespace Dragonzap\TwoFactorAuthentication;

use Illuminate\Support\Facades\Session;

class TwoFactorAuthentication
{

    private static $handlerInstance = null;

    public static function getHandlerInstance(): TwoFactorAuthenticationHandlerInterface
    {
        if (self::$handlerInstance == null) {
            self::$handlerInstance = new (config('dragonzap_2factor.authentication.handler.class'))();
        }

        return self::$handlerInstance;
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

    private static function validateAuthenticationType($type)
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
}

