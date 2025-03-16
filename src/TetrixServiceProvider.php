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
        $this->app['router']->pushMiddlewareToGroup('web', \Tetrix\Middlewares\Tetrix::class);
    }
}