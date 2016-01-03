<?php

Route::group(['prefix' => 'v1'], function () {

    get('/', [
        'as'   => 'v1.home',
        'uses' => Appkr\Fractal\Example\LinkController::class . '@index'
    ]);

    get('doc', [
        'as' => 'v1.doc',
        function () {
            return "Placeholder for v1 api documentation page.";
        }
    ]);

    resource(
        'things',
        Appkr\Fractal\Example\ThingsController::class,
        ['except' => ['create', 'edit']]
    );

    get('authors/{id}/things', [
        'as'   => 'v1.authors.things',
        'uses' => Appkr\Fractal\Example\ThingsController::class . '@index'
    ]);

    resource(
        'authors',
        Appkr\Fractal\Example\AuthorsController::class,
        ['only' => ['index', 'show']]
    );
});
