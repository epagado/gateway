<?php

namespace Epagado;

use Epagado\Gateway;
use Illuminate\Support\ServiceProvider;

class EpagadoServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes(
            [
                __DIR__ . '/../config.php' => config_path('redsys.php'),
            ], 'epagado-config'
        );
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('gateway', function () {
            return new Gateway();
        });

        // Merge default config values
        $this->mergeConfigFrom(__DIR__.'/../config.php', 'epagado');

    }
}