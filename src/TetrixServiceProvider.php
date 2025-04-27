<?php

namespace Tetrix;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redirect;

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

        // Override the default redirect back method to support modal redirects
        Redirect::macro('back', function ($status = 302, $headers = [], $fallback = false) {
            /** @var \Illuminate\Routing\Redirector $this */
            $request = request();

            if ($modalUrl = $request->header('TX-Modal-Referer')) {
                return $this->to($modalUrl, 303, $headers);
            }

            $referer = $request->headers->get('referer');

            return $this->to($referer ?: ($fallback ?: '/'), $status, $headers);
        });

        // Register middleware
        $this->app['router']->pushMiddlewareToGroup('web', \Tetrix\Middlewares\TetrixTargets::class);
        $this->app['router']->pushMiddlewareToGroup('web', \Tetrix\Middlewares\TetrixRedirect::class);
    }
}