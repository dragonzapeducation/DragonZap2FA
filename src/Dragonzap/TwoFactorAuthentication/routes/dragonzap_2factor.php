<?php

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dragonzap/two_factor/generate', 'Dragonzap\TwoFactorAuthentication\Controllers\TwoFactorAuthenticationController@twoFactorGenerateCode')->name('dragonzap.two_factor_generate_code');
    Route::get('/dragonzap/two_factor/enter', 'Dragonzap\TwoFactorAuthentication\Controllers\TwoFactorAuthenticationController@twoFactorEnterCode')->name('dragonzap.two_factor_enter_code');
    Route::post('/dragonzap/two_factor/confirm', 'Dragonzap\TwoFactorAuthentication\Controllers\TwoFactorAuthenticationController@confirmTwoFactorCode')->name('dragonzap.two_factor_confirm_code');
});