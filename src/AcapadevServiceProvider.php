<?php

namespace Acapadev\Sdk;

use Illuminate\Support\ServiceProvider;
use Acapadev\Sdk\Console\InstallCommand;

class AcapadevServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/acapadev.php' => config_path('acapadev.php'),
        ], 'acapadev-config');

        $this->loadRoutesFrom(__DIR__.'/routes/webhooks.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/acapadev.php', 'acapadev'
        );
    }
}
