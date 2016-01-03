<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Endpoint pattern
    |--------------------------------------------------------------------------
    |
    | Path 'pattern' used for bypassing the CSRF token check.
    | Provide 'domain', if our api route is distinguished by domain name.
    |
    */
    'pattern' => 'v1/*',
    'domain'  => 'api.example.com',

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
    | Refer to
    | http://fractal.thephpleague.com/serializers/
    |
    */
    'serializer' => \League\Fractal\Serializer\ArraySerializer::class,

    /*
    |--------------------------------------------------------------------------
    | Default Response Headers
    |--------------------------------------------------------------------------
    |
    | Default response headers that every resource/simple response should includes
    |
    */
    'defaultHeaders' => [],

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
            'code'    => ':code',
            'message' => ':message',
        ]
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
    'errorFormat' =>  [
        'error' => [
            'code'    => ':code',
            'message' => ':message',
        ]
    ]

];
