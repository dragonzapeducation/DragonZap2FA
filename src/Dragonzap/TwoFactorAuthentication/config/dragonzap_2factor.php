<?php

/*
 * Licensed under GPLv2
 * Author: Daniel McCarthy
 * Dragon Zap Publishing
 * Website: https://dragonzap.com
 */

 
return [
   'enabled' => env('DRAGONZAP_2FACTOR_ENABLED', true),
   'notification' => [
    // You can change the notification subject for the TwoFactorCodeNotification class here
    'subject' => 'Confirm your two factor authentication code',
    // For more customization options, you can create your own notification class and set it here
    // Allowing you to extend to sms messages, slack, etc.
    'class' => \Dragonzap\TwoFactorAuthentication\Notifications\TwoFactorCodeNotification::class,
   ]
];