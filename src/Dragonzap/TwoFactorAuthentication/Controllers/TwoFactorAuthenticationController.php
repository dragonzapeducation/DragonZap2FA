<?php

namespace Dragonzap\TwoFactorAuthentication\Controllers;

use Dragonzap\TwoFactorAuthentication\TwoFactorAuthentication;

class TwoFactorAuthenticationController
{
    public function twoFactorGenerateCode()
    {

        // Generate a new code
        $two_factor_code = TwoFactorAuthentication::generateCode();
        // Send it to the user
        $two_factor_code->send();
        return redirect()->route('dragonzap.two_factor_enter_code');
    }

    public function twoFactorEnterCode()
    {
        // View that asks for the code received.
        return view('dragonzap_2factor::enter_code');
    }

    public function confirmTwoFactorCode()
    {
        if (!request()->has('code')) {
            return redirect()->back()->withErrors(['code' => 'No code was provided']);
        }

        $code = request()->get('code');

        $two_factor_code = TwoFactorAuthentication::getGeneratedCode();
        if (!$two_factor_code || !$two_factor_code->isValid()) {
            return redirect()->back()->withErrors(['code' => 'The code is invalid or expired.']);
        }

        $okay = $two_factor_code->confirm($code);
        if ($okay) {
            // Release the authentication requirement for this session and redirect to the return URL
            TwoFactorAuthentication::releaseAuthRequirement();
            // Enable two factor authentication if its not enabled.
            auth()->user()->two_factor_enabled = true;
            auth()->user()->save();
            return redirect()->to(TwoFactorAuthentication::getReturnUrl());
        } else {
            return redirect()->back()->withErrors(['code' => 'The code is incorrect']);
        }
    }
}