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

        $this->loadViewsFrom(__DIR__.'/views', 'dragonzap_2factor');

        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/dragonzap_2factor'),
        ], 'views');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->loadRoutesFrom(__DIR__.'/routes/dragonzap_2factor.php');
        $this->publishes([
            __DIR__.'/routes/web.php' => base_path('routes/dragonzap_2factor.php'),
        ], 'routes');

        

    }
    
    public function register()
    {
        // Code for bindings, if necessary
    }
}

