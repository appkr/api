<?php

Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
    Route::get('/', [
        'as'   => 'home',
        'uses' => Appkr\Api\Example\LinkController::class . '@index',
    ]);

    Route::get('doc', [
        'as' => 'doc',
        function () {
            return "Placeholder for v1 api documentation page.";
        },
    ]);

    Route::resource(
        'books',
        Appkr\Api\Example\BooksController::class,
        ['except' => ['create', 'edit']]
    );

    Route::get('authors/{id}/books', [
        'as'   => 'authors.books',
        'uses' => Appkr\Api\Example\BooksController::class . '@index',
    ]);

    Route::resource(
        'authors',
        Appkr\Api\Example\AuthorsController::class,
        ['only' => ['index', 'show']]
    );
});
