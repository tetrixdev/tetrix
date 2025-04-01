<?php

namespace Tetrix;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class TetrixServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * Put things here when you don't need any other services to be available
     *
     * @return void
     */
    public function register()
    {
        // Register commands
        $this->commands([
            \Tetrix\Commands\InstallDependenciesThroughNpm::class,
        ]);

        // Merge package config
        $this->mergeConfigFrom(__DIR__.'/Config/tetrix.php', 'tetrix');

        // Load Component views and register the classes
        $this->loadViewsFrom(__DIR__.'/Components/Views', 'tx');
        Blade::componentNamespace('Tetrix\\Components\\Classes', 'tx');

        // Load general views
        $this->loadViewsFrom(__DIR__.'/Views', 'tx');
    }

    /**
     * Bootstrap any application services.
     * Put things here when you need other services to be available
     *
     * @return void
     */
    public function boot()
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/Config/tetrix.php' => config_path('tetrix.php'),
        ], 'tetrix-config');

        // Register middleware
        $this->app['router']->pushMiddlewareToGroup('web', \Tetrix\Middlewares\Tetrix::class);
    }
}