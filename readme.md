# RESTful HTTP API component for Laravel or Lumen based project

[![Latest Stable Version](https://poser.pugx.org/appkr/api/v/stable)](https://packagist.org/packages/appkr/api) 
[![Total Downloads](https://poser.pugx.org/appkr/api/downloads)](https://packagist.org/packages/appkr/api) 
[![Latest Unstable Version](https://poser.pugx.org/appkr/api/v/unstable)](https://packagist.org/packages/appkr/api) 
[![License](https://poser.pugx.org/appkr/api/license)](https://packagist.org/packages/appkr/api)

**[한국어 매뉴얼](readme_ko.md)**

## INDEX

-   [1. ABOUT](#about)
-   [2. FEATURE](#goal)
-   [3. LARAVEL/LUMEN IMPLEMENTATION EXAMPLE](#example)
-   [4. HOW TO INSTALL](#install)
-   [5. CONFIG](#config)
-   [6. TRANSFORMER](#transformer)
-   [7. NESTING SUB-RESOURCE](#nesting)
-   [8. PARTIAL RESPONSE](#partial)
-   [9. APIs](#method)
-   [10. BUNDLED EXAMPLE](#example)
-   LICENSE & CONTRIBUTION

---

<a name="about"></a>
## 1. ABOUT

A lightweight RESTful API builder for Laravel or/and Lumen project.

<a name="goal"></a>
## 2. FEATURE

1. Provides Laravel/Lumen Service Provider for the `league/fractal`.
2. Provides configuration capability for the library.
3. Provides easy way of making transformed/serialized API response.
4. Provides `make:transformer` artisan command.
5. Provides examples, so that users can quickly copy &amp; paste into his/her project.

<a name="example"></a>
## 3. LARAVEL/LUMEN IMPLEMENTATION EXAMPLE(How to use)

### 3.1. API Endpoint

Define RESTful resource route in Laravel way.

```php
// app/Http/routes.php

Route::group(['prefix' => 'v1'], function () {
    Route::resource(
        'books',
        'BooksController',
        ['except' => ['create', 'edit']]
    );
});
```

Lumen doesn't support RESTful resource route. You have to define them one by one.

```
// app/Http/routes.php

$app->group(['prefix' => 'v1'], function ($app) {
    $app->get('books', [
        'as'   => 'v1.books.index',
        'uses' => 'BooksController@index',
    ]);
    $app->get('books/{id}', [
        'as'   => 'v1.books.show',
        'uses' => 'BooksController@show',
    ]);
    $app->post('books', [
        'as'   => 'v1.books.store',
        'uses' => 'BooksController@store',
    ]);
    $app->put('books/{id}, [
       'as'   => 'v1.books.update',
       'uses' => 'BooksController@update',
   ]);
    $app->delete('books/{id}', [
       'as'   => 'v1.books.destroy',
       'uses' => 'BooksController@destroy',
   ]);
});
```

### 3.2. Controller

The subsequent code block is the controller logic for `/v1/books/{id}` endpoint. Note the use cases of `json()` helper and transformer on the following code block.

```php
// app/Http/Controllers/BooksController.php

<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Book;
use App\Transformers\BookTransformer;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function index()
    {
        return json()->withPagination(
            Book::latest()->paginate(5),
            new BookTransformer
        );
    }

    public function store(Request $request)
    {
        // Assumes that validation is done at somewhere else
        return json()->created(
            $request->user()->create($request->all())
        );
    }

    public function show($id)
    {
        return json()->withItem(
            Book::findOrFail($id),
            new BookTransformer
        );
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        return ($book->update($request->all()))
            ? json()->success('Updated')
            : json()->error('Failed to update');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        return ($book->delete())
            ? json()->success('Deleted')
            : json()->error('Failed to delete');
    }
}
```

<a name="install"></a>
## 4. HOW TO INSTALL

### 4.1. Composer.

```sh
$ composer require "appkr/api: 1.*"
```

### 4.2. Add the service provider.

```php
// config/app.php (Laravel)

'providers'=> [
    Appkr\Api\ApiServiceProvider::class,
]
```

```php
// boostrap/app.php (Lumen)

$app->register(Appkr\Api\ApiServiceProvider::class);
```

### 4.3. [OPTIONAL] Publish assets.

```sh
# Laravel only
$ php artisan vendor:publish --provider="Appkr\Api\ApiServiceProvider"
```

The configuration file is located at `config/api.php`.

Done !

<a name="config"></a>
## 5. CONFIG

Skim through the [`config/api.php`](https://github.com/appkr/api/blob/master/src/config/api.php), which is inline documented.

<a name="transformer"></a>
## 6. TRANSFORMER

### 6.1. What?

For more about what the transformer is, what you can do with this, and why it is required, [see this page](http://fractal.thephpleague.com/transformers/). 1 transformer for 1 model is a best practice(e.g. `BookTransformer` for `Book` model). 

### 6.2. Transformer Boilerplate Generator

Luckily this package ships with an artisan command that conveniently generates a transformer class.

```sh
$ php artisan make:transformer {subject} {--includes=}
# e.g. php artisan make:transformer "App\Book" --includes="App\\User:author,App\\Comment:comments:true"
```

-   `subject`_ The string name of the model class.

-   `includes`_ Sub-resources that is related to the subject model. By providing this option, your API client can have control over the response body. see [NESTING SUB RESOURCES](#nesting) section. 

    The option's signature is `--include=Model,eloquent_relationship_methods[,isCollection]`. 
    
    If the include-able sub-resource is a type of collection, like `Book` and `Comment` relationship in the example, we provide `true` as the third value of the option.

> **`Note`** 
>
> We should always use double back slashes (`\\`), when passing a namespace in artisan command WITHOUT quotation marks.
>
>```sh
> $ php artisan make:transformer App\\Book --includes=App\\User:author,App\\Comment:comments:true
> ```

A generated file will look like this:

```php
<?php
namespace App\Transformers;

use App\Book;
use Appkr\Api\TransformerAbstract;
use League\Fractal;
use League\Fractal\ParamBag;

class BookTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include using url query string.
     * e.g. collection case -> ?include=comments:limit(5|1):order(created_at|desc)
     *      item case       -> ?include=author
     *
     * @var  array
     */
    protected $availableIncludes = [
        'author', 
        'comments'
    ];
    
    /**
     * Transform single resource.
     *
     * @param  \App\Book $book
     * @return  array
     */
    public function transform(Book $book)
    {
        $payload = [
            'id' => (int) $book->id,
            // ...
            'created' => $book->created_at->toIso8601String(),
            'link' => [
                 'rel' => 'self',
                 'href' => route('api.v1.books.show', $book->id),
            ],
        ];
    }

    /**
     * Include author.
     * This method is used, when an API client request /v1/books?include=author
     *
     * @param  \App\Book $book
     * @param \League\Fractal\ParamBag|null $params
     * @return  \League\Fractal\Resource\Item
     */
    public function includeAuthor(Book $book, ParamBag $params = null)
    {
        return $this->item(
            $book->author, 
            new \App\Transformers\UserTransformer($params)
        );
    }
    
    /**
     * Include comments.
     * This method is used, when an API client request /v1/books??include=comments
     *
     * @param  \App\Book $book
     * @param  \League\Fractal\ParamBag|null $params
     * @return  \League\Fractal\Resource\Collection
     */
    public function includeComments(Book $book, ParamBag $params = null)
    {
        $transformer = new \App\Transformers\CommentTransformer($params);

        $parsed = $transformer->getParsedParams();

        $comments = $book->comments()
            ->limit($parsed['limit'])
            ->offset($parsed['offset'])
            ->orderBy($parsed['sort'], $parsed['order'])
            ->get();

        return $this->collection($comments, $transformer);
    }
}
```

<a name="nesting"></a>
## 7. NESTING SUB-RESOURCES

An API client can request a resource with its sub-resource. The following example is requesting `authors` list. At the same time, it requests each author's `books` list. It also has additional parameters, which reads as 'I need total of 3 books for this author when ordered by recency without any skipping'.

```HTTP
GET /authors?include=books:limit(3|0):sort(id|desc)
```

When including multiple sub resources,

```HTTP
GET /authors?include[]=books:limit(2|0)&include[]=comments:sort(id|asc)

# or alternatively

GET /authors?include=books:limit(2|0),comments:sort(id|asc)
```

In case of deep recursive nesting, use dot (`.`). In the following example, we assume the publisher model has relationship with somethingelse model.

```HTTP
GET /books?include=author,publisher.somethingelse
```

<a name="partial"></a>
## 8. PARTIAL RESPONSE

An API client can designate the fields that s/he wants to receive. The following example illustrates the situation where the client wants to receive only id, name, and email fields, with sub-resource of the author's book collection. The client also limits the fields of sub-resource as id, title, and published_at.

```HTTP
GET /authors?fields=id,name,email&include=books:limit(3|0):fields(id|title|published_at)
```

Note that, for parent resource, we used comma (,) as the field delimiter , while pipe (|) was used for children.  

<a name="method"></a>
## 9. APIs

The following is the full list of response methods that `Appkr\Api\Http\Response` provides. Really handy when making a json response in a controller.

### 9.1. `Appkr\Api\Response` - Available Methods

```php
// Generic response. 
// If valid callback parameter is provided, jsonp response can be provided.
// This is a very base method. All other responses are utilizing this.
respond(array $payload);

// Respond collection of resources
// If $transformer is not given as the second argument,
// this class does its best to transform the payload to a simple array
withCollection(
    \Illuminate\Database\Eloquent\Collection $collection, 
    \League\Fractal\TransformerAbstract|null $transformer, 
    string|null $resourceKey // for JsonApiSerializer only
);

// Respond single item
withItem(
    \Illuminate\Database\Eloquent\Model $model, 
    \League\Fractal\TransformerAbstract|null $transformer, 
    string|null $resourceKey // for JsonApiSerializer only
);

// Respond collection of resources with pagination
withPagination(
    \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator, 
    \League\Fractal\TransformerAbstract|null $transformer, 
    string|null $resourceKey // for JsonApiSerializer only
);

// Respond json formatted success message
// api.php provides configuration capability
success(string|array $message);

// Respond 201
// If an Eloquent model is given at an argument,
// the class tries its best to transform the model to a simple array
created(string|array|\Illuminate\Database\Eloquent\Model $primitive);

// Respond 204
noContent();

// Respond 304
notModified();

// Generic error response
// This is another base method. Every other error responses use this.
// If an instance of \Exception is given as an argument,
// this class does its best to properly format a message and status code
error(string|array|\Exception|null $message);

// Respond 401
// Note that this actually means unauthenticated
unauthorizedError(string|array|null $message);

// Respond 403
// Note that this actually means unauthorized
forbiddenError(string|array|null $message);

// Respond 404
notFoundError(string|array|null $message);

// Respond 405
notAllowedError(string|array|null $message);

// Respond 406
notAcceptableError(string|array|null $message);

// Respond 409
conflictError(string|array|null $message);

// Respond 410
goneError(string|array|null $message);

// Respond 422
unprocessableError(string|array|null $message);

// Respond 500
internalError(string|array|null $message);

// Set http status code
// This method is chain-able
setStatusCode(int $statusCode);

// Set http response header
// This method is chain-able
setHeaders(array $headers);

// Set additional meta data
// This method is chain-able
setMeta(array $meta);
```

### 9.2. `Appkr\Api\TransformerAbstract` - Available Methods

```php
// We can apply this method against an instantiated transformer,
// to get the parsed query parameters that belongs only to the current resource.
// 
// e.g. GET /v1/author?include[]=books:limit(2|0):fields(id|title|published_at)&include[]=comments:sort(id|asc)
//      $transformer = new BookTransformer;
//      $transformer->get(); 
// Will produce all parsed parameters:
//      // [
//      //     'limit'  => 2 // if not given default value at config
//      //     'offset' => 0 // if not given default value at config
//      //     'sort'   => 'created_at' // if given, given value
//      //     'order'  => 'desc' // if given, given value
//      //     'fields' => ['id', 'title', 'published_at'] // if not given, null
//      // ]
// Alternatively we can pass a key. 
//      $transformer->get('limit');
// Will produce limit parameter:
//      // 2
get(string|null $key)

// Exactly does the same function as get.
// Was laid here, to enhance readability.
getParsedParams(string|null $key)
```

### 9.3. `helpers.php` - Available Functions

```php
// Make JSON response
// Returns Appkr\Api\Http\Response object if no argument is given,
// from there you can chain any public apis that are listed above.
json(array|null $payload)

// Determine if the current framework is Laravel
is_laravel();

// Determine if the current framework is Lumen
is_lumen();

// Determine if the current request is for API endpoints, and expecting API response
is_api_request();

// Determine if the request is for update
is_update_request();

// Determine if the request is for delete
is_delete_request();
```

<a name="example"></a>
## 10. BUNDLED EXAMPLE

The package is bundled with a set of example that follows the best practices. It includes:

-   Database migrations and seeder
-   routes definition, Eloquent Model and corresponding Controller
-   FormRequest *(Laravel only)*
-   Transformer
-   Integration Test

Follow the guide to activate and test the example.

### 10.1. Activate examples

Uncomment the line.

```php
// vendor/appkr/api/src/ApiServiceProvider.php

$this->publishExamples();
```

### 10.2. Migrate and seed tables

Do the following to make test table and seed test data. Highly recommend to use SQLite, to avoid polluting the main database of yours.

```sh
$ php artisan migrate --path="vendor/appkr/api/src/example/database/migrations" --database="sqlite"
$ php artisan db:seed --class="Appkr\Api\Example\DatabaseSeeder" --database="sqlite"
```

### 10.3. See it works

Boot up a server.

```sh
$ php artisan serve
```

Head on to `GET /v1/books`, and you should see a well formatted json response. Try each route to get accustomed to, such as `/v1/books=include=authors`, `/v1/authors=include=books:limit(2|0):order(id|desc)`.

![](resources/appkr-api-example-img-01.png)

### 10.4. [OPTIONAL] Run integration test

```sh
# Laravel
$ vendor/bin/phpunit vendor/appkr/api/src/example/BookApiTestForLaravel.php
```

```sh
# Lumen
$ vendor/bin/phpunit vendor/appkr/api/src/example/BookApiTestForLumen.php
```

> **`Note`** 
> 
> If you finished evaluating the example, don't forget to rollback the migration and re-comment the unnecessary lines at `ApiServiceProvider`.

## 11. LICENSE & CONTRIBUTION

[MIT License](https://raw.githubusercontent.com/appkr/api/master/LICENSE). Issues and PRs are always welcomed.
