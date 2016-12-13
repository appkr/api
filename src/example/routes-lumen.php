<?php

use Appkr\Api\Example\AuthorsController;
use Appkr\Api\Example\BooksControllerForLumen;

$app->group(['prefix' => 'v1'], function ($app) {
    $app->get('doc', [
        'as' => 'v1.doc',
        function () {
            return "Placeholder for v1 api documentation page.";
        },
    ]);

    $app->get('books', [
        'as' => 'v1.books.index',
        'uses' => BooksControllerForLumen::class . '@index',
    ]);
    $app->get('books/{id}', [
        'as' => 'v1.books.show',
        'uses' => BooksControllerForLumen::class . '@show',
    ]);
    $app->post('books', [
        'as' => 'v1.books.store',
        'uses' => BooksControllerForLumen::class . '@store',
    ]);
    $app->put('books/{id}', [
        'as' => 'v1.books.update',
        'uses' => BooksControllerForLumen::class . '@update',
    ]);
    $app->delete('books/{id}', [
        'as' => 'v1.books.destroy',
        'uses' => BooksControllerForLumen::class . '@destroy',
    ]);

    $app->get('authors', [
        'as' => 'v1.authors.index',
        'uses' => AuthorsController::class . '@index',
    ]);
    $app->get('authors/{id}', [
        'as' => 'v1.authors.show',
        'uses' => AuthorsController::class . '@show',
    ]);
});
