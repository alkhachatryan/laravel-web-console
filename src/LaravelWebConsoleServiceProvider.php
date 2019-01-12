<?php

namespace Alkhachatryan\LaravelWebConsole;

use Illuminate\Support\ServiceProvider;

class LaravelWebConsoleServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerHelpers();
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'webconsole');
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        $this->publishes([
            __DIR__.'/../config/laravelwebconsole.php' => config_path('laravelwebconsole.php'),
        ], 'webconsole');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelwebconsole.php', 'laravelwebconsole');

        $this->app->singleton('laravelwebconsole', function ($app) {
            return new LaravelWebConsole;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelwebconsole'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        $this->publishes([
            __DIR__.'/../config/laravelwebconsole.php' => config_path('laravelwebconsole.php'),
        ], 'laravelwebconsole.config');
    }

    /**
     * Register helpers file.
     */
    public function registerHelpers()
    {
        // Load the helpers in app/Http/helpers.php
        if (file_exists($file = __DIR__.'/../helpers.php')) {
            require_once $file;
        }
    }
}
