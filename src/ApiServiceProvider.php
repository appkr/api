<?php

namespace Appkr\Api;

use Appkr\Api\Http\Response;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager as Fractal;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadStubs();
        $this->publishConfig();

        // Uncomment below to activate the example
        // $this->publishExamples();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Fractal::class, function ($app) {
            $config = $app['config'];

            $fractal = new Fractal;
            $fractal->setSerializer(
                app($config->get('api.serializer'))
            );

            $includes = $app['request']->input($config->get('api.include.key'));

            if ($includes) {
                $fractal->parseIncludes($includes);
            }

            return $fractal;
        });

        $this->app->alias(Fractal::class, 'api.provider');

        $this->app->bind('api.response', Response::class);

        $this->registerMakeTransfomerCommand();
    }

    /**
     * Load stub files as the form of blade view.
     */
    protected function loadStubs()
    {
        $this->loadViewsFrom(__DIR__ . '/./resources/stubs', 'api');
    }

    /**
     * Publish stub files which will be used as a template
     * for make:transformer artisan command.
     */
//    protected function publishStubs()
//    {
//        $this->publishes([
//            __DIR__ . '/./resources/stubs' => resource_path('views/vendor/api'),
//        ]);
//    }

    /**
     * Publish Config.
     * api.php to config/api.php
     */
    protected function publishConfig()
    {
        $this->publishes([
            realpath(__DIR__ . '/./config/api.php') => base_path('config/api.php'),
        ]);

        $this->mergeConfigFrom(
            realpath(__DIR__ . '/./config/api.php'),
            'api'
        );
    }

    /**
     * Publish examples
     */
    protected function publishExamples()
    {
        $this->publishes([
            realpath(__DIR__ . '/./example/database/migrations') => database_path('migrations'),
            realpath(__DIR__ . '/./example/factories/factories')  => database_path('factories'),
        ]);

        if (is_laravel()) {
            include __DIR__ . '/./example/routes.php';
        } elseif (is_lumen()) {
            $app = $this->app;
            include __DIR__ . '/./example/routes-lumen.php';
        }
    }

    /**
     * Register make:transformer command.
     */
    protected function registerMakeTransfomerCommand()
    {
        $this->app->singleton('api.make.transformer', function ($app) {
            return $app['Appkr\Api\Commands\MakeTransformerCommand'];
        });

        $this->commands('api.make.transformer');
    }
}
