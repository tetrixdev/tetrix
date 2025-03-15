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

        // Load views
        $this->loadViewsFrom(__DIR__.'/Components/Views', 'tx');

        // Register the classes
        Blade::componentNamespace('Tetrix\\Components\\Classes', 'tx');
    }

    /**
     * Bootstrap any application services.
     * Put things here when you need other services to be available
     *
     * @return void
     */
    public function boot()
    {
        $this->app['router']->pushMiddlewareToGroup('web', \Tetrix\Middlewares\Tetrix::class);
    }
}