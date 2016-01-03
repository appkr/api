<?php

namespace Appkr\Fractal;

use Appkr\Fractal\Http\Response;
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
            $fractal = new Fractal;
            $fractal->setSerializer(app($app['config']['fractal']['serializer']));

            return $fractal;
        });

        $this->app->alias(Fractal::class, 'api.provider');

        $this->app->bind('api.response', Response::class);

        $this->registerMakeTransfomerCommand();
    }

    /**
     * Publish Config.
     * fractal.php to config/fractal.php
     */
    protected function publishConfig()
    {
        $this->publishes([
            realpath(__DIR__ . '/../config/fractal.php') => base_path('config/fractal.php')
        ]);

        $this->mergeConfigFrom(
            realpath(__DIR__ . '/../config/fractal.php'),
            'fractal'
        );
    }

    /**
     * Publish examples
     */
    protected function publishExamples()
    {
        $this->publishes([
            realpath(__DIR__ . '/../database/migrations/') => database_path('migrations'),
            realpath(__DIR__ . '/../database/factories/')  => database_path('factories')
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
            return $app['Appkr\Fractal\Commands\MakeTransformerCommand'];
        });
        $this->commands('api.make.transformer');
    }
}
