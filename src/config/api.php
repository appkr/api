<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    |
    | If set to true, debug information will be include in api response.
    | Must set to false for production.
    |
    */
    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | API Endpoint pattern
    |--------------------------------------------------------------------------
    |
    | Path 'pattern' used for is_api_request() Helper.
    | Provide 'domain', if the api routes are distinguished by domain name.
    |
    */
    'endpoint' => [
        'pattern' => 'v1/*',
        'domain' => env('API_DOMAIN', 'api.example.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Include by query string.
    |--------------------------------------------------------------------------
    |
    | If you defined 'availableInclude' property and includeXxx methods
    | in a transformer, you can include sub resources using query string.
    | e.g. /authors?include=books:limit(3|0):order(id|desc) means
    | including 3 records of 'authors', which is reverse ordered by 'id' field,
    | without any skipping(0).
    |
    | An API client can pass list of includes using array or csv string format.
    | e.g. /authors?include[]=books:limit(2|0)&include[]=comments:order(id|asc)
    |      /authors?include=books:limit(2|0),comments:order(id|asc)
    |
    | For sub-resource inclusion, client can use dot(.) notation.
    | e.g. /books?include=author,publisher.somethingelse
    |
    */
    'include' => [
        'key' => 'include',
        'params' => [ // available modifier params and their default value
            'limit' => [3, 0], // [limit, offset]
            'sort' => ['created_at', 'desc'], // [sortKey, sortDirection]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transformer directory and namespace.
    |--------------------------------------------------------------------------
    |
    | Below config will be applied when we run 'make:transformer' artisan cmd.
    | The generated class will be saved at 'dir', and namespaced as you set.
    | Note that the 'dir' should be relative to the project root.
    |
    */
    'transformer' => [
        'dir' => 'app/Transformers',
        'namespace' => 'App\\Transformers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fractal Serializer
    |--------------------------------------------------------------------------
    |
    | for 'serializer' refer to
    | http://fractal.thephpleague.com/serializers/
    | for 'jsonEncodeOption' refer to
    | http://php.net/manual/kr/json.constants.php
    |
    */
    'serializer' => \League\Fractal\Serializer\ArraySerializer::class,
    'jsonEncodeOption' => 0,

    /*
    |--------------------------------------------------------------------------
    | Default Response Headers
    |--------------------------------------------------------------------------
    |
    | Default response headers that every resource/simple response should includes
    |
    */
    'defaultHeaders' => [
        // 'foo' => 'bar'
    ],

    /*
    |--------------------------------------------------------------------------
    | Suppress HTTP status code
    |--------------------------------------------------------------------------
    |
    | If set to true, the status code will be fixed to 200.
    |
    */
    'suppress_response_code' => false,

    /*
    |--------------------------------------------------------------------------
    | Success Response Format
    |--------------------------------------------------------------------------
    |
    | The format will be used at the ApiResponse to respond with success message.
    | respondNoContent(), respondSuccess(), respondCreated() consumes this format
    |
    */
    'successFormat' => [
        'success' => [
            'code' => ':code',
            'message' => ':message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Response Format
    |--------------------------------------------------------------------------
    |
    | The format will be used at the ApiResponse to respond with error message.
    | respondWithError(), respondForbidden()... consumes this format
    |
    */
    'errorFormat' => [
        'error' => [
            'code' => ':code',
            'message' => ':message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Formatting
    |--------------------------------------------------------------------------
    |
    | Convert field key and date value value. Set these to null if you want to
    | control the response body manually in transformer.
    |
    | Available value for key: null or 'camel_case' or 'snake_case'
    | Available value for date: null or
    |   @see http://php.net/manual/en/class.datetime.php#datetime.constants.types
    |
    */
    'convert' => [
        'key' => 'snake_case',
        'date' => DATE_ISO8601
    ],
];
