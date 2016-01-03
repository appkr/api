## `league/fractal` WRAPPER FOR LARAVEL/LUMEN 5#

[![Latest Stable Version](https://poser.pugx.org/appkr/fractal/v/stable)](https://packagist.org/packages/appkr/fractal) 
[![Total Downloads](https://poser.pugx.org/appkr/fractal/downloads)](https://packagist.org/packages/appkr/fractal) 
[![Latest Unstable Version](https://poser.pugx.org/appkr/fractal/v/unstable)](https://packagist.org/packages/appkr/fractal) 
[![License](https://poser.pugx.org/appkr/fractal/license)](https://packagist.org/packages/appkr/fractal)

# INDEX

- [ABOUT](#about)
- [GOAL OF THIS PACKAGE](#goal)
- [LARAVEL/LUMEN IMPLEMENTATION EXAMPLE](#example)
- [HOW TO INSTALL](#install)
- [TRANSFORMER](#transformer)
- [CONFIG](#config)
- [APIs](#method)
- [BUNDLED EXAMPLE](#example)

---

<a name="about"></a>
## ABOUT

This is a package, or rather an **opinionated/laravelish use case of the famous [`league/fractal`](https://github.com/thephpleague/fractal) package for Laravel 5 and Lumen**. This package was started to fulfill a personal RESTful API service needs. And provided as a separate package, hoping users quickly build his/her RESTful API. 

Among **1. METHOD**, **2. RESOURCE**, and **3. RESPONSE**, which is 3 pillars of REST, this package is mainly focusing on a **3. RESPONSE(=view layer)**. By reading this readme and following along the bundled examples, I hope you understand REST principles, and build a beautiful APIs that everybody can understand easily.

<a name="goal"></a>
## GOAL OF THIS PACKAGE

1. Provides easy access to the `league/fractal`'s core instance (via ServiceProvider facility).
2. Provides easy way of making transformed/serialized http response.
3. Provides make:transformer artisan command.
4. Provides configuration capability for the response format.
5. Provides examples, so that users can quickly copy &amp; paste into his/her project.

<a name="example"></a>
## LARAVEL/LUMEN IMPLEMENTATION EXAMPLE

**1. METHOD** and **2. RESOURCE** can be easily handled by Laravel/Lumen routes file.

### Define METHOD and RESOURCE in Laravel way.

```php
// app/Http/routes.php

Route::resource(
    'things',
    ThingsController::class,
    ['except' => ['create', 'edit']]
);
```

### Fill missing feature for RESTful API with Response class

Corresponding to Laravel/Lumen route definition, you can implement your controller in RESTful fashion by injecting `Appkr\Fractal\Http\Response` or with `json()` Helper (**3. RESPONSE**).

### Use Case

```php
// app/Http/Controllers/ThingsController.php

<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThingsRequest;
use App\Thing;
use App\Transformers\ThingTransformer;

class ThingsController extends Controller
{
    public function index()
    {
        return json()->withPagination(
            Thing::latest()->paginate(25),
            new ThingTransformer
        );
    }

    public function store(ThingsRequest $request)
    {
        return json()->created(Thing::create(array_merge(
            $request->all(),
            $request->user()->id
        )));
    }

    public function show($id)
    {
        return json()->withItem(
            Thing::findOrFail($id),
            new ThingTransformer
        );
    }

    public function update(ThingsRequest $request, $id)
    {
        $thing = Thing::findOrFail($id);

        return ($thing->update($request->all()))
            ? json()->success('Updated')
            : json()->error('Fail to update');
    }

    public function destroy($id)
    {
        $thing = Thing::findOrFail($id);

        return ($thing->delete())
            ? json()->success('Deleted')
            : json()->error('Fail to delete');
    }
}
```

<a name="install"></a>
##HOW TO INSTALL

### **Setp #1:** Composer.

```bash
$ composer require "appkr/fractal: 0.6.*"
```

### **Step #2:** Add the service provider.

```php
// config/app.php (Laravel)
'providers'=> [
    Appkr\Fractal\ApiServiceProvider::class,
]

// boostrap/app.php (Lumen)
$app->register(Appkr\Fractal\ApiServiceProvider::class);
```

### **Step #3:** [OPTIONAL] Publish assets.

```bash
# Laravel only
$ php artisan vendor:publish --provider="Appkr\Fractal\ApiServiceProvider"
```

The config file is located at `config/fractal.php`.

Done !

<a name="transformer"></a>
## TRANSFORMER

### What?

You should implement it by yourself. For more about what is it, what you can do with this, and why it is neeeded, [see this page](http://fractal.thephpleague.com/transformers/)

### Generator

Luckily this package ships with an artisan command conveniently generates a transformer class.

```bash
$ php artisan make:transformer {subject} {--includes=}
```

- `subject` : The string name of the model class. e.g. App\\\\Book
- `includes` : Optional list of resources to include. e.g. App\\\\User:author,App\\\\Comment:comments:true. If the third element is provided as true, yes, or 1, the command will interpret the include as a collection.

**`Note`** You should always use double back slashes (\\\\), when passing class name in artisan command.

A generated file will look like this:

```php
<?php

namespace App\Transformers;

use App\Book;
use League\Fractal;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class BookTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include using url query string.
     * e.g. collection case -> ?include=comments:limit(5|1):order(created_at|desc)
     *      item case       -> ?include=author
     *
     * @var array
     */
    protected $availableIncludes = ['author', 'comments'];

    /**
     * List of resources to include automatically/always.
     *
     * @var array
     */
    protected $defaultIncludes = ['author', 'comments'];

    /**
     * List of extra parameters when including other resources.
     * This is only applicable when including a collection.
     */
    private $validParams = ['limit', 'order'];

    /**
     * Transform single resource.
     *
     * @param \App\Book $book
     * @return array
     */
    public function transform(Book $book)
    {
        return [
            'id' => (int) $book->id,
            // ...
            'created' => $book->created_at->toIso8601String(),
            'link' => [
                 'rel' => 'self',
                 'href' => route('books.show', $book->id),
            ],
        ];
    }

    /**
     * Include author.
     *
     * @param \App\Book $book
     * @return \League\Fractal\Resource\Item
     */
    public function includeAuthor(Book $book)
    {
        return $this->item($book->author, new \App\Transformers\UserTransformer);
    }
    
    /**
     * Include comments.
     *
     * @param \App\Book $book
     * @param \League\Fractal\ParamBag
     * @return \League\Fractal\Resource\Item
     * @throws \Exception
     */
    public function includeComments(Book $book, ParamBag $params)
    {
        $usedParams = array_keys(iterator_to_array($params));

        if ($invalidParams = array_diff($usedParams, $this->validParams)) {
            throw new \Exception(sprintf('Invalid param(s): "%s". Valid param(s): "%s"', implode(',', $usedParams), implode(',', $this->validParams)));
        }

        list($limit, $offset) = $params->get('limit') ?: [5,1];
        list($orderCol, $orderBy) = $params->get('order') ?: ['created_at', 'desc'];

        $comments = $book->comments
            ->take($limit)
            ->skip($offset)
            ->orderBy($orderCol, $orderBy)
            ->get();

        return $this->collection($comments, new \App\Transformers\CommentTransformer);
    }
}
```

<a name="transformer"></a>
## CONFIG

Skim thorough the `config/fractal.php`. Inline documented. I think I did my best in articulating for each config.

<a name="method"></a>
## APIs

The following is the full list of response methods that `Appkr\Fractal\Http\Response` provides. You should use in `YourController` to format API response.

### AVAILABLE RESPONSE METHODS

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
// fractal.php provides configuration capability
success(string|array $message);

// Respond 201
// If an Eloquent model is given at an argument,
// the class tries its best to transform the model to a simple array
created(string|array|\Illuminate\Database\Eloquent\Model $primitive);

// Respond 204
noContent();

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

// Respond 406
notAcceptableError(string|array|null $message);

// Respond 409
conflictError(string|array|null $message);

// Respond 422
unprocessableError(string|array|null $message);

// Respond 500
internalError(string|array|null $message);

// Set http status code
// This method is chainable
setStatusCode(int $statusCode);

// Set http response header
// This method is chainable
setHeaders(array $headers);

// Set additional meta data
// This method is chainable
setMeta(array $meta);
```

###AVAILABLE HELPER METHODS
```php
// Make JSON response
// Returns Appkr\Fractal\Http\Response object if no argument given,
// from there you can chain any methods listed subsequently.
json(array|null $payload)

// Determine if the current framework is Laravel
is_laravel();

// Determine if the current framework is Lumen
is_lumen();

// Determine if the current version of framework is based on 5.1
is_51();

// Determine if the current request is generated from an api client
is_api_request();

// Determine if the request is for update
is_update_request();

// Determine if the request is for delete
is_delete_request();
```

<a name="example"></a>
##BUNDLED EXAMPLE

Easiest way to learn this package and what RESTful is, I bet. The package is bundled with a set of example. It includes:

- Database migrations and seeder
- routes definition, Eloquent Model and corresponding Controller
- FormRequest *(Laravel only)*
- Transformer
- Integration Test

Follow the guide to activate and test the example.

### **Step #1:** Activate examples

```php
// Uncomment the line at vendor/appkr/fractal/src/ApiServiceProvider.php

$this->publishExamples();
```

### **Step #2:** Migrate and seed tables

```bash
# Migrate/seed tables at a console

$ php artisan migrate --path="vendor/appkr/fractal/database/migrations"
$ php artisan db:seed --class="Appkr\Fractal\Example\DatabaseSeeder"
```

### **Step #3:** Boot up a server and open at a browser

```bash
# Boot up a server

$ php artisan serve
```

Head on to `http://localhost:8000/v1/things`, and you should see a well formatted json response.

### **Step #4:** [OPTIONAL] Run integration test

```bash
# Laravel
$ phpunit vendor/appkr/fractal/src/example/ThingApiTestForLaravel.php

# Lumen
$ phpunit vendor/appkr/fractal/src/example/ThingApiTestForLumen.php
```

**`Note`** _If you finished evaluating the example, don't forget to rollback the migration and re-comment the unnecessary lines at `ApiServiceProvider`._

---

##LICENSE & CONTRIBUTION

This package follows [MIT License](https://raw.githubusercontent.com/appkr/fractal/master/LICENSE). Issues and PRs are welcomed.
