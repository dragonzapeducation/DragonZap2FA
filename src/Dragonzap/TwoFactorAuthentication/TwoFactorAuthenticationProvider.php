<?php

/*
 * Licensed under GPLv2
 * Author: Daniel McCarthy
 * Email: daniel@dragonzap.com
 * Dragon Zap Publishing
 * Website: https://dragonzap.com
 */

namespace Dragonzap\TwoFactorAuthentication;

use Illuminate\Support\ServiceProvider;

class TwoFactorAuthenticationProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/dragonzap_2factor.php' => config_path('dragonzap_2factor.php'),
        ], 'config');
    
        $this->mergeConfigFrom(
            __DIR__.'/config/dragonzap_2factor.php', 'dragonzap_2factor'
        );

        $this->loadMigrationsFrom(__DIR__.'./database/migrations');

    }
    
    public function register()
    {
        // Code for bindings, if necessary
    }
}

