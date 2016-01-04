<?php

Route::group(['prefix' => 'v1'], function () {

    Route::get('/', [
        'as'   => 'v1.home',
        'uses' => Appkr\Api\Example\LinkController::class . '@index',
    ]);

    Route::get('doc', [
        'as' => 'v1.doc',
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
        'as'   => 'v1.authors.books',
        'uses' => Appkr\Api\Example\BooksController::class . '@index',
    ]);

    Route::resource(
        'authors',
        Appkr\Api\Example\AuthorsController::class,
        ['only' => ['index', 'show']]
    );
});
