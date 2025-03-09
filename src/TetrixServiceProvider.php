<?php

namespace Tetrix;

use Illuminate\Support\ServiceProvider;
use Tetrix\Commands\InstallDependenciesThroughNpm;

class TetrixServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register commands
        $this->commands([
            InstallDependenciesThroughNpm::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Bootstrap any application services here
    }
}