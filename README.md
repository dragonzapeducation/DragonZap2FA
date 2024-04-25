# DragonZap2FA
This is a Laravel two factor authentication package which is defaulted to provide two factor authentication through email but also supports TOTP(Time based one time password) for use in authenticator applications such as microsoft authenticator and google authenticator. The classes can be overrided and the functionality is fully customizeable allowing you to provide 2FA to your users. Adding 2FA is as easy as assigning the route middlewear "twofactor" as seen in the below example

```
  Route::group(['middleware' => ['auth','twofactor:always']], function () {
        // Sales Routes
        Route::get('user/orders', 'Backend\IncomeController@index')->name('user.orders');
  });

```

You can provide the "always" tag to always require two factor authentication even if the user has not enabled two factor authentication. You can provide the tag "if-enabled" for situations where you will only two factor if the user has enabled two factor authentication. Upon a user completing two factor authentication it will be enabled on his account 

## Installing the DragonZAP2FA package
Now that you have seen how easy it is to use this package lets begin with the installation. First install [Composer](https://getcomposer.org) which will allow you to install our package. Once composer is installed you must run the following command in your laravel directory:
```
composer install dragonzap/2fa ^2.0
```

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
```

These settings allow you to change the messages, notification class along with how long 2FA will last before its required again for the protected pages

## Different route middleware
- **"twofactor:always"** Authentication will be required for the given route regardless if the user has 2FA enabled, once authenticated 2FA will automatically be switched on for the users account. Functionality can be modified by extending TwoFactorAuthenticationHandler class and overriding the authenticationCompleted method. Then update the handler class in the configuration file.
- **"twofactor:if-enabled"** Authentication will be required for the given route only if the user has two factor authentication enabled. 
- **"twofactor:only-once-if-enabled"** If applied on a route then authentication will be required only once in the entire user session for that route to be accessible. User can authenticate on any route to be able to pass this check, so long as they have authenticated on any route in the current session. User will not be required to authenticate if they do not have 2FA enabled.
- **"twofactor:only-once-always"** Works the same as **twofactor:only-once-if-enabled** except all users regardless if they have enabled  2FA will be required to authenticate. Once authenticated 2FA will be enabled on their user account.


## Enabling two factor authentication on a user account

To enable two factor authentication on a user account set the "two_factor_enabled" column to true for the given user record.

## Editing the views
You can find the views in your views/vendor/dragonzap directory where you can modify the views for the blade files that allow users to enter the two factor codes.

## Example controller for managing a users two factor authentication settings
The package only manages two factor authentication for authorization no controller is provided for allowing the 2FA to be enabled on user accounts. See the below example controller on how to do this. As can be seen the migrations created by this package create two columns in the users table two_factor_enabled and two_factor_type. You can manipualte these values to enable two factor authentication for your users
```
<?php

namespace App\Http\Controllers\Backend\Auth\User;

use App\Http\Controllers\Controller;
use Dragonzap\TwoFactorAuthentication\TwoFactorAuthentication;
use Illuminate\Http\Request;


/**
 * Class AccountController.
 */
class TwoFactorAuthenticationController extends Controller
{
    public function updateTwoFactorAuthenticationEnabled(Request $request)
    {
        $user = auth()->user();

        if ($request->has('two_factor_enabled') && $request->get('two_factor_enabled') == '1'){
            $user->two_factor_enabled = true;
            $user->save();

            return redirect()->route('admin.account')->withFlashSuccess(__('Two Factor Authentication has been enabled.'))->with('tab', 'two-factor-auth');
        }

        $user->two_factor_enabled = false;
        $user->save();

        return redirect()->back()->withFlashSuccess( __('Two Factor Authentication has been disabled your account is no longer secure.'))->with('tab', 'two-factor-auth');
    }

    public function changeTwoFactorAuthenticationType(Request $request)
    {
        $user = auth()->user();

        if ($request->has('two_factor_type') && $request->get('two_factor_type') == 'totp'){
            $user->two_factor_type = 'totp';
            $user->save();

            // Generate a new TOTP for this user and delete old TOTPS
            TwoFactorAuthentication::generateTotpForUser(auth()->user(), NULL, true);

            return redirect()->route('admin.account')->withFlashSuccess(__('Two Factor Authentication type has been changed to TOTP.'))->with('tab', 'two-factor-auth');
        }

        $user->two_factor_type = 'otp';
        $user->save();

        return redirect()->route('admin.account')->withFlashSuccess(__('Two Factor Authentication type has been changed to email codes.'))->with('tab', 'two-factor-auth');
    }

    public function confirmTOTPAuthentication(Request $request)
    {
        $user = auth()->user();

        if ($request->has('code')){
            $totp = TwoFactorAuthentication::getTotpsForUser($user)->first();
            // By veryfing the code correctly we are also confirming the TOTP, making it active
            // after the first use which is now it becomes a valid TOTP
            if ($totp && $totp->verify($request->get('code'))){
                return redirect()->route('admin.account')->withFlashSuccess(__('Two Factor Authentication has been confirmed do not lose your authenticator app!'))->with('tab', 'two-factor-auth');
            }
        }

        // Return error for the code field, affecting the errors bundle
        return redirect()->route('admin.account')->withErrors(['code' => __('The code you entered is invalid.')])->with('tab', 'two-factor-auth');
    }
}

```

## Authentication types
The two_factor_type column in the users table determines the type of two factor authentication that should be used with the user when the two_factor_enabled column is true
* otp - This is the old fashioned style OTP and by default will send an email to the user unless this functionality is overrided.
* totp - This is the modern TOTP which requires authenticator applications to be used, set the user two_factor_type column to **totp** to require the use of authenticator applications for that user.


## Confirmed TOTP vs unconfirmed TOTP
When a TOTP is generated with the **TwoFactorAuthentication::generateTotpForUser** method the generated TOTP will be in an inactive unconfirmed state, if the user has the two_factor_type set to "totp" to signify that the authenticaton apps will be required but has not confirmed a TOTP code before then authentication will be switched to "otp" for the authentication request, by default sending an email to the user. Only when a TOTP is confirmed will the user be prompted for the code on an authentication app. This prevents the user from enabling TOTP and forgetting to scan the QR code leading them to be locked out of their account, therefore as a pracutionary measure the default OTP will be used in this senario.

## How to confirm a TOTP?
A TOTP can be confirmed by setting the "confirmed" column on the **TwoFactorTotp** particular model record associated with the user account. You should only confirm the TOTP once the user has proven they have scanned the QR code. To prove the user has done this you ask them to enter the code on the authenticator app and then verify it. Upon verifying the TOTP code the record will automatically be updated to be confirmed ensuring that TOTP is used for all future authentications.

## Example code to confirm a TOTP
```
// Get the first TOTP associated with the user account, users can have many TOTPs
      $totp = TwoFactorAuthentication::getTotpsForUser($user)->first();
      // Call the verify funnction 
            if ($totp && $totp->verify($request->get('code'))){
                return redirect()->route('admin.account')->withFlashSuccess(__('Two Factor Authentication has been confirmed do not lose your authenticator app!'))->with('tab', 'two-factor-auth');
            }

```
upon calling the **verify** method if the verification was correct then the confirmed column in the TOTP record will be set to true allowing this TOTP to be used for all future authentication.
