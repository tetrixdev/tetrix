<?php

namespace Tetrix;

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