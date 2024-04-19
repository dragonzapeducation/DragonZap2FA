<?php

namespace Dragonzap\TwoFactorAuthentication\Controllers;

use Dragonzap\TwoFactorAuthentication\Models\TwoFactorTotp;
use Dragonzap\TwoFactorAuthentication\TwoFactorAuthentication;

class TwoFactorAuthenticationController
{
    public function twoFactorGenerateCode()
    {

        // Generate a new code
        $two_factor_code = TwoFactorAuthentication::generateCode();
        // Send it to the user
        $two_factor_code->send();
        return redirect()->route('dragonzap.two_factor_enter_code')->with('success', config('dragonzap_2factor.messages.code_sent'));
    }

    public function twoFactorEnterCode()
    {
        if (auth()->user()->two_factor_type == 'otp') {
            // View that asks for the code received.
            return view('dragonzap_2factor::enter_code');
        }

        if (auth()->user()->two_factor_type == 'totp') {
            $totps = TwoFactorTotp::forUser(auth()->user())->orderBy('created_at', 'desc')->get();

            // View that asks for the code received.
            return view('dragonzap_2factor::enter_totp_code', compact('totps'));
        }

        abort(500, 'Invalid two factor type of user account contact support');
    }

    public function confirmTwoFactorCode()
    {
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
}