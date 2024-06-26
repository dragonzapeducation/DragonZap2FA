<?php

namespace Dragonzap\TwoFactorAuthentication;

use Dragonzap\TwoFactorAuthentication\Exceptions\InvalidAuthenticationTypeException;
use Illuminate\Support\Facades\Session;

class TwoFactorAuthenticationHandler implements TwoFactorAuthenticationHandlerInterface
{

    public function setAuthenticatingUser($user) : void
    {
        Session::put('two_factor_user_id', auth()->user()->id);
        Session::put('two_factor_ip', request()->ip());
        Session::save();
    }

    public function generateCode(): TwoFactorCode
    {
        // Secure random
        $random_code = random_int(100000, 999999);
        Session::put('two_factor_code', $random_code);
        Session::put('two_factor_code_time', now());

        Session::save();
        return new TwoFactorCode($random_code, now(), auth()->user()->id);
    }

    /**
     * Sets the return URL for the two factor authentication process. I.e the URL to return to after the user has authenticated
     */
    public function setReturnUrl(string $url): void
    {
        Session::put('two_factor_return_url', $url);
        Session::save();
    }
    /**
     * Gets the return URL for the two factor authentication process
     */
    public function getReturnUrl(): string
    {
        // We must deal with senarios where the return URL is the two factor authentication URL
        if (Session::get('two_factor_return_url') == route('dragonzap.two_factor_generate_code') ||
            Session::get('two_factor_return_url') == route('dragonzap.two_factor_enter_code'))
        {
            // Just go to home..
            return app('url')->to('/');
        }
        return Session::get('two_factor_return_url');
    }

    /**
     * Releases the two factor authentication requirement for the session, after some time
     * this will expire and authentication will be required again.
     * @return bool
     */
    public function releaseAuthRequirement(): void
    {
        $this->clearCode();
        Session::put('two_factor_authenticated', true);
        Session::put('two_factor_authenticated_time', now());

        Session::save();
    }

    public function validateAuthenticationType($type)
    {
        if ($type != 'if-enabled' && $type != 'always' && $type != 'only-once-if-enabled' && $type != 'only-once-always') {
            throw new InvalidAuthenticationTypeException('Invalid authentication type');
        }
    }

    /**
     * Checks if the user needs to authenticate with two factor authentication
     * @param string $type This is the type of authentication required. 'if-enabled' means that the user must authenticate if two factor authentication is enabled, 'always' means that the user must always authenticate with two factor authentication regardless of the setting.
     * @throws DragonZap\TwoFactorAuthentication\Exceptions\InvalidAuthenticationTypeException if the type is invalid or not recognized
     */
    public function isAuthenticationRequired($type = 'if-enabled'): bool
    {
        $this->validateAuthenticationType($type);
        if (!config('dragonzap_2factor.enabled')) {
            return false;
        }

        if ($type == 'if-enabled' || $type == 'only-once-if-enabled') {
            if (!auth()->user()->two_factor_enabled) {
                return false;
            }
        }

        if (!Session::has('two_factor_authenticated') || !Session::get('two_factor_authenticated')) {
            return true;
        }


        // Has the IP address changed? If so, require authentication
        if (request()->ip() != Session::get('two_factor_ip')) {
            return true;
        }

        // Has the user id changed?
        if (auth()->user()->id != Session::get('two_factor_user_id')) {
            return true;
        }

        // If the type is 'only-once' and the user has authenticated before, then we never have to authenticate
        // this route again, because the user has already authenticated once before.
        if ($type == 'only-once-if-enabled' || $type == 'only-once-always') {
            if ($this->hasAuthenticatedBefore()) {
                return false;
            }
        }

        // Not an only once? Then we need to check if the last time the user authenticated has expired

        // Has the last time the user authenticated expired?
        $authenticated_time = Session::get('two_factor_authenticated_time');
        return $authenticated_time->diffInMinutes(now()) >= config('dragonzap_2factor.authentication.expires_in_minutes');
    }

    public function clearCode()
    {
        Session::forget('two_factor_code');
        Session::forget('two_factor_code_time');
        Session::save();
    }


    public function getGeneratedCode(): TwoFactorCode|null
    {
        if (!Session::has('two_factor_code')) {
            return null;
        }

        return new TwoFactorCode(Session::get('two_factor_code'), Session::get('two_factor_code_time'), Session::get('two_factor_user_id'));
    }

    public function hasExistingCode(): bool
    {
        $generated_code = $this->getGeneratedCode();
        return $generated_code !== null && $generated_code->isValid();
    }

    public function authenticationCompleted(): void
    {
        // Release the authentication requirement for this session and redirect to the return URL
        $this->releaseAuthRequirement();

        // Enable two factor authentication if its not enabled.
        auth()->user()->two_factor_enabled = true;
        auth()->user()->save();
    }

    public function hasAuthenticatedBefore(): bool
    {
        return Session::has('two_factor_authenticated') && Session::get('two_factor_authenticated');
    }

}

