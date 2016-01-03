<?php

$app->group(['prefix' => 'v1'], function ($app) {
    $app->get('doc', [
        'as' => 'v1.doc',
        function () {
            return "Placeholder for v1 api documentation page.";
        }
    ]);

    $app->get('things', [
        'as'   => 'v1.things.index',
        'uses' => \Appkr\Fractal\Example\ThingsControllerForLumen::class . '@index'
    ]);
    $app->get('things/{id}', [
        'as'   => 'v1.things.show',
        'uses' => \Appkr\Fractal\Example\ThingsControllerForLumen::class . '@show'
    ]);
    $app->post('things', [
        'as'   => 'v1.things.store',
        'uses' => \Appkr\Fractal\Example\ThingsControllerForLumen::class . '@store'
    ]);
    $app->put('things/{id}', [
        'as'   => 'v1.things.update',
        'uses' => \Appkr\Fractal\Example\ThingsControllerForLumen::class . '@update'
    ]);
    $app->delete('things/{id}', [
        'as'   => 'v1.things.destroy',
        'uses' => \Appkr\Fractal\Example\ThingsControllerForLumen::class . '@destroy'
    ]);

    $app->get('authors', [
        'as'   => 'v1.authors.index',
        'uses' => \Appkr\Fractal\Example\AuthorsController::class . '@index'
    ]);
    $app->get('authors/{id}', [
        'as'   => 'v1.authors.show',
        'uses' => \Appkr\Fractal\Example\AuthorsController::class . '@show'
    ]);
});
