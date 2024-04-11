<?php

Route::get('/dragonzap/two_factor', 'Dragonzap\TwoFactorAuthentication\Controllers\TwoFactorAuthenticationController@twoFactorEnterCode')->name('dragonzap.two_factor_enter_code');
