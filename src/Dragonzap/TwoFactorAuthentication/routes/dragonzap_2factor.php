<?php

Route::get('/dragonzap/two_factor', 'TwoFactorAuthenticationController@twoFactorEnterCode')->name('dragonzap.two_factor_enter_code');
