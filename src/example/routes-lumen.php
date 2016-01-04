<?php

$app->group(['prefix' => 'v1'], function ($app) {
    $app->get('doc', [
        'as' => 'v1.doc',
        function () {
            return "Placeholder for v1 api documentation page.";
        },
    ]);

    $app->get('books', [
        'as'   => 'v1.books.index',
        'uses' => \Appkr\Api\Example\BooksControllerForLumen::class . '@index',
    ]);
    $app->get('books/{id}', [
        'as'   => 'v1.books.show',
        'uses' => \Appkr\Api\Example\BooksControllerForLumen::class . '@show',
    ]);
    $app->post('books', [
        'as'   => 'v1.books.store',
        'uses' => \Appkr\Api\Example\BooksControllerForLumen::class . '@store',
    ]);
    $app->put('books/{id}', [
        'as'   => 'v1.books.update',
        'uses' => \Appkr\Api\Example\BooksControllerForLumen::class . '@update',
    ]);
    $app->delete('books/{id}', [
        'as'   => 'v1.books.destroy',
        'uses' => \Appkr\Api\Example\BooksControllerForLumen::class . '@destroy',
    ]);

    $app->get('authors', [
        'as'   => 'v1.authors.index',
        'uses' => \Appkr\Api\Example\AuthorsController::class . '@index',
    ]);
    $app->get('authors/{id}', [
        'as'   => 'v1.authors.show',
        'uses' => \Appkr\Api\Example\AuthorsController::class . '@show',
    ]);
});
