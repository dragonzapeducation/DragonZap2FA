<?php

/*
 * Licensed under GPLv2
 * Author: Daniel McCarthy
 * Dragon Zap Publishing
 * Website: https://dragonzap.com
 */

 
return [
   'enabled' => env('DRAGONZAP_2FACTOR_ENABLED', true),
   'authentication' => [
       'expires_in_minutes' => env('DRAGONZAP_2FACTOR_AUTHENTICATION_EXPIRES_IN_MINUTES', 15),
   ],
   'messages' => [
    'code_sent' => 'A code has been sent to you. Please enter it below.',
    'code_invalid' => 'The code is invalid or expired.',
    'no_code_provided' => 'No code was provided.',
    'code_incorrect' => 'The code is incorrect.',
   ],
   'notification' => [
    // You can change the notification subject for the TwoFactorCodeNotification class here
    'subject' => 'Confirm your two factor authentication code',
    // For more customization options, you can create your own notification class and set it here
    // Allowing you to extend to sms messages, slack, etc.
    'class' => \Dragonzap\TwoFactorAuthentication\Notifications\TwoFactorCodeNotification::class,
   ]
];