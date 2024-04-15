# DragonZap2FA
This is a Laravel two factor authentication package which is defaulted to provide two factor authentication through email. The classes can be overrided and the functionality is fully customizeable allowing you to provide 2FA to your users. Adding 2FA is as easy as assigning the route middlewear "twofactor" as seen in the below example

```
  Route::group(['middleware' => ['auth','twofactor:always']], function () {
        // Sales Routes
        Route::get('user/orders', 'Backend\IncomeController@index')->name('user.orders');
  });

```

You can provide the "always" tag to always require two factor authentication even if the user has not enabled two factor authentication. You can provide the tag "if-enabled" for situations where you will only two factor if the user has enabled two factor authentication. Upon a user completing two factor authentication it will be enabled on his account 

## Installing the DragonZAP2FA package
Now that you have seen how easy it is to use this package lets begin with the installation. First install [Composer](https://getcomposer.org) which will allow you to install our package. Once composer is installed you must run the following command in your laravel directory:
```composer install dragonzap/2fa```

## Publishing the vendor files
Next you need to publish the vendor files 
```
php artisan vendor:publish --provider="Dragonzap\TwoFactorAuthentication\TwoFactorAuthenticationProvider" 

```

## Migrating the database
With these changes completed you now need to migrate the Laravel database
```
php artisan migrate
```

This will ensure that the changes to the user table take place to allow 2FA to be enabled or disabled for them


## Changing the configuration
Upon completing all these steps the package is installed, you can customize the configuration file found at ./config/dragonzap_2fa.php
```
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
   ],
   'notification' => [
    // You can change the notification subject for the TwoFactorCodeNotification class here
    'subject' => 'Confirm your two factor authentication code',
    // For more customization options, you can create your own notification class and set it here
    // Allowing you to extend to sms messages, slack, etc.
    'class' => \Dragonzap\TwoFactorAuthentication\Notifications\TwoFactorCodeNotification::class,
   ],
   
];
```

These settings allow you to change the messages, notification class along with how long 2FA will last before its required again for the protected pages

## Different route middlewear 
- **"twofactor:always"** Authentication will be required for the given route regardless if the user has 2FA enabled, once authenticated 2FA will automatically be switched on for the users account. Functionality can be modified by extending TwoFactorAuthenticationHandler class and overriding the authenticationCompleted method. Then update the handler class in the configuration file.
- **"twofactor:if-enabled"** Authentication will be required for the given route only if the user has two factor authentication enabled. 
- **"twofactor:only-once-if-enabled"** If applied on a route then authentication will be required only once in the entire user session for that route to be accessible. User can authenticate on any route to be able to pass this check, so long as they have authenticated on any route in the current session. User will not be required to authenticate if they do not have 2FA enabled.
- **"twofactor:only-once-always"** Works the same as **twofactor:only-once-if-enabled** except all users regardless if they have enabled  2FA will be required to authenticate. Once authenticated 2FA will be enabled on their user account.


## Enabling two factor authentication on a user account

To enable two factor authentication on a user account set the "two_factor_enabled" column to true for the given user record.


