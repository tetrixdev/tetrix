<?php

namespace Tetrix;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

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
        // Loading helper functions
        require __DIR__ . '/helpers.php';

        // Publish config file
        $this->publishes([
            __DIR__.'/Config/tetrix.php' => config_path('tetrix.php'),
        ], 'tetrix-config');

        // Override the default redirect back method to support modal redirects
        Redirect::macro('back', function ($status = 302, $headers = [], $fallback = false) {
            /** @var \Illuminate\Routing\Redirector $this */
            $request = request();

            if ($modalUrl = $request->header('TX-Referer')) {
                return $this->to($modalUrl, 303, $headers);
            }

            $referer = $request->headers->get('referer');

            return $this->to($referer ?: ($fallback ?: '/'), $status, $headers);
        });

        // Logic for running validation when doing Precognition
//        $this->app->resolving(FormRequest::class, function (FormRequest $request) {
//            Log::debug('FormRequest resolving');
//            if ($request->header('Precognition') === 'true') {
//                Log::debug('precog, start validation');
//                // Trigger validation now (Laravel won't automatically do it here)
//                $request->validateResolved();
//
//                Log::debug('precog, validation done without errors');
//                Log::debug('precog, old values: '.json_encode(old()));
//                Log::debug('precog, input values: '.json_encode($request->input()));
//
//                // Flash the input to the session
////                $request->flash();
////                $request->session()->flash('errors', ['test' => 'test']);
//
//                // Redirect back to the original URL, use redirect->back()
//                // Middleware should take care in case referer header was set
//                $response = response()->redirectTo(tx_referer())->setStatusCode(303)->withInput($request->input());
//                $response->send();
//            }
//        });

        // Logic for running validation when doing Precognition
        $this->app->resolving(FormRequest::class, function (FormRequest $request) {
            Log::debug('FormRequest resolving');
            if ($request->header('Precognition') === 'true') {
                Log::debug('precog, start validation');
                // Trigger validation now (Laravel won't automatically do it here)
                $request->validateResolved();

                Log::debug('precog, validation done without errors');
                Log::debug('precog, old values: ' . json_encode(old()));
                Log::debug('precog, input values: ' . json_encode($request->input()));

                // Return a 303 redirect with input flashed
                abort(
                    redirect(tx_referer())->withInput()->setStatusCode(303)
                );
            }
        });

        // Register middleware
        $this->app['router']->pushMiddlewareToGroup('web', \Tetrix\Middlewares\TetrixTargets::class);
        $this->app['router']->pushMiddlewareToGroup('web', \Tetrix\Middlewares\TetrixRedirect::class);
    }
}
