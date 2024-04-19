<?php

/*
 * Licensed under GPLv2
 * Author: Daniel McCarthy
 * Dragon Zap Publishing
 * Website: https://dragonzap.com
 */

 
return [
   'enabled' => env('DRAGONZAP_2FACTOR_ENABLED', true),
   'totp' => [
       'issuer' => env('DRAGONZAP_2FACTOR_TOTP_ISSUER', 'MyExampleApp'),
       'model' => \Dragonzap\TwoFactorAuthentication\Models\TwoFactorTotp::class,
       'messages' => [
        'check_code' => 'Please check your authenticator app for the code.',
        'invalid_authenticator' => 'Misconfigured authenticator contact support',
        'code_invalid' => 'The code is invalid or expired.',
        'no_code_provided' => 'No code was provided.',
        'code_incorrect' => 'The code is incorrect.',
        'wrong_2fa_type' => 'A different two factor type is required for this user account.',
        'no_totp_id_provided' => 'No TOTP ID was provided.',
        'invalid_totp_id' => 'Invalid TOTP ID provided.',
       ]
   ],
   'authentication' => [
       'expires_in_minutes' => env('DRAGONZAP_2FACTOR_AUTHENTICATION_EXPIRES_IN_MINUTES', 15),
       'handler' => [
        // This is the authentication handler class, you can override your own class here
        // if you wish to have custom functionality
        'class' => \Dragonzap\TwoFactorAuthentication\TwoFactorAuthenticationHandler::class,
       ]
   ],
   'messages' => [
    'code_sent' => 'A code has been sent to you. Please enter it below.',
    'code_invalid' => 'The code is invalid or expired.',
    'no_code_provided' => 'No code was provided.',
    'code_incorrect' => 'The code is incorrect.',
    'wrong_2fa_type' => 'A different two factor type is required for this user account.',
   ],
   'notification' => [
    // You can change the notification subject for the TwoFactorCodeNotification class here
    'subject' => 'Confirm your two factor authentication code',
    // For more customization options, you can create your own notification class and set it here
    // Allowing you to extend to sms messages, slack, etc.
    'class' => \Dragonzap\TwoFactorAuthentication\Notifications\TwoFactorCodeNotification::class,
   ],
   
];