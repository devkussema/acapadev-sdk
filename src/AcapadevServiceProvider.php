<?php

namespace Acapadev\Sdk;

use Illuminate\Support\ServiceProvider;
use Acapadev\Sdk\Console\InstallCommand;
use Acapadev\Sdk\Console\PublishDocsCommand;

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
                PublishDocsCommand::class,
            ]);
        }

        $this->bootSocialite();
    }

    /**
     * Register the Socialite provider.
     */
    protected function bootSocialite(): void
    {
        if (class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');

            $socialite->extend('acapadev', function ($app) use ($socialite) {
                $config = [
                    'client_id' => config('acapadev.client_id'),
                    'client_secret' => config('acapadev.client_secret'),
                    'redirect' => config('acapadev.redirect'),
                ];

                return $socialite->buildProvider(
                    \Acapadev\Sdk\Socialite\AcapadevProvider::class, $config
                );
            });
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

        $this->app->singleton(\Acapadev\Sdk\Services\AcapadevApiClient::class, function ($app) {
            return new \Acapadev\Sdk\Services\AcapadevApiClient();
        });
    }
}
