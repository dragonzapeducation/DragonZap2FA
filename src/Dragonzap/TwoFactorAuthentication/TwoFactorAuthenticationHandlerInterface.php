<?php

namespace Dragonzap\TwoFactorAuthentication;

use Dragonzap\TwoFactorAuthentication\TwoFactorCode;

interface TwoFactorAuthenticationHandlerInterface
{
    public function generateCode(): TwoFactorCode;

    /**
     * Sets the return URL for the two factor authentication process. I.e the URL to return to after the user has authenticated
     */
    public function setReturnUrl(string $url): void;
    /**
     * Gets the return URL for the two factor authentication process
     */
    public function getReturnUrl(): string;
    /**
     * Releases the two factor authentication requirement for the session, after some time
     * this will expire and authentication will be required again.
     * @return bool
     */
    public  function releaseAuthRequirement(): void;
    public function validateAuthenticationType($type);
    /**
     * Checks if the user needs to authenticate with two factor authentication
     * @param string $type This is the type of authentication required. 'if-enabled' means that the user must authenticate if two factor authentication is enabled, 'always' means that the user must always authenticate with two factor authentication regardless of the setting.
     * @throws DragonZap\TwoFactorAuthentication\Exceptions\InvalidAuthenticationTypeException if the type is invalid or not recognized
     */
    public function isAuthenticationRequired($type = 'if-enabled'): bool;
    public function clearCode();
    public function getGeneratedCode(): TwoFactorCode|null;

    public function hasExistingCode(): bool;

    public function authenticationCompleted() : void;

    public function hasAuthenticatedBefore() : bool;

}

