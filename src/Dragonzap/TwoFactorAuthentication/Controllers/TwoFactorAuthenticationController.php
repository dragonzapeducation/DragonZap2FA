<?php

namespace Dragonzap\TwoFactorAuthentication\Controllers;

use Dragonzap\TwoFactorAuthentication\Exceptions\InvalidAuthenticationTypeException;
use Dragonzap\TwoFactorAuthentication\Models\TwoFactorTotp;
use Dragonzap\TwoFactorAuthentication\TwoFactorAuthentication;

class TwoFactorAuthenticationController
{
    public function twoFactorGenerateCode()
    {

        // Set the authenticating user this prevents a user from authenticating on behalf of another.
        TwoFactorAuthentication::setAuthenticatingUser(auth()->user());


        // TOTP just needs to be entered right away no need to generate any codes...
        if (TwoFactorAuthentication::getTwoFactorTypeForUser(auth()->user()) == 'totp') {
            return redirect()->route('dragonzap.two_factor_enter_code');
        }

        if (TwoFactorAuthentication::getTwoFactorTypeForUser(auth()->user()) == 'otp') {
            // Generate a new code
            $two_factor_code = TwoFactorAuthentication::generateCode();
            // Send it to the user
            $two_factor_code->send();
            return redirect()->route('dragonzap.two_factor_enter_code')->with('success', config('dragonzap_2factor.messages.code_sent'));
        }

        throw new InvalidAuthenticationTypeException('Invalid or unsupported two factor type of user account');
    }

    public function twoFactorEnterCode()
    {
        if (TwoFactorAuthentication::getTwoFactorTypeForUser(auth()->user()) == 'otp') {
            // View that asks for the code received.
            return view('dragonzap_2factor::enter_code');
        }

        if (TwoFactorAuthentication::getTwoFactorTypeForUser(auth()->user()) == 'totp') {
            $totps = TwoFactorTotp::forUser(auth()->user())->confirmedOnly()->orderBy('created_at', 'desc')->get();

            // View that asks for the code received.
            return view('dragonzap_2factor::enter_totp_code', compact('totps'));
        }

        throw new InvalidAuthenticationTypeException('Invalid or unsupported two factor type of user account');
    }

    public function confirmTwoFactorCode()
    {
        // If the standard OTP type is not selected then they wont be able to authenticate
        // through the use of OTP's that are sent to emails, phone numbers etc.
        if (TwoFactorAuthentication::getTwoFactorTypeForUser(auth()->user()) != 'otp') {
            return redirect()->back()->withErrors(['code' => 'Invalid two factor type of user account']);
        }

        if (!request()->has('code')) {
            return redirect()->back()->withErrors(['code' => config('dragonzap_2factor.messages.no_code_provided')]);
        }

        $code = request()->get('code');
        // If the code has been sent as an array of numbers (e.g. [1, 2, 3, 4]), convert it to a string
        if (is_array($code)) {
            $code = implode('', $code);
        }

        $two_factor_code = TwoFactorAuthentication::getGeneratedCode();
        if (!$two_factor_code || !$two_factor_code->isValid()) {
            return redirect()->back()->withErrors(['code' => config('dragonzap_2factor.messages.code_expired')]);
        }

        $okay = $two_factor_code->confirm($code);
        if ($okay) {
            TwoFactorAuthentication::authenticationCompleted();
            return redirect()->to(TwoFactorAuthentication::getReturnUrl());
        } else {
            return redirect()->back()->withErrors(['code' => config('dragonzap_2factor.messages.code_incorrect')]);
        }
    }

    public function twoFactorEnterTotpCodeSubmit()
    {

        // We wont allow TOTP authentication if the user has not selected TOTP as their two factor type
        if (TwoFactorAuthentication::getTwoFactorTypeForUser(auth()->user()) != 'totp') {
            return redirect()->back()->withErrors(['code' => config('dragonzap_2factor.messages.wrong_2fa_type')]);
        }


        if (!request()->has('totp_id')) {
            return redirect()->back()->withErrors(['totp_id' => config('dragonzap_2factor.totp.messages.no_totp_id_provided')]);
        }

        
        $totp_id = request()->get('totp_id');
        $totp = TwoFactorTotp::find($totp_id);
        if (!$totp) {
            return redirect()->back()->withErrors(['totp_id' => config('dragonzap_2factor.totp.messages.invalid_totp_id')]);
        }

        
        // Only allow the user to confirm their own TOTP
        if ($totp->user_id != auth()->user()->id) {
            return redirect()->back()->withErrors(['totp_id' => config('dragonzap_2factor.totp.messages.invalid_totp_id')]);
        }

        if (!request()->has('code')) {
            return redirect()->back()->withErrors(['code' => config('dragonzap_2factor.totp.messages.no_code_provided')]);
        }


        $code = request()->get('code');
        // If the code has been sent as an array of numbers (e.g. [1, 2, 3, 4]), convert it to a string
        if (is_array($code)) {
            $code = implode('', $code);
        }
        
        try {
            if (!$totp->verify($code)) {
                return redirect()->back()->withErrors(['code' => config('dragonzap_2factor.totp.messages.code_invalid')]);
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['code' => config('dragonzap_2factor.totp.messages.invalid_authenticator')]);
        }

        TwoFactorAuthentication::authenticationCompleted();
        return redirect()->to(TwoFactorAuthentication::getReturnUrl());
    }
}