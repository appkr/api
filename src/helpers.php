<?php

if (! function_exists('is_laravel')) {
    /**
     * Determine the current framework is Laravel
     *
     * @return bool
     */
    function is_laravel()
    {
        return file_exists(base_path('vendor/laravel/framework/readme.md'));
    }
}

if (! function_exists('is_lumen')) {
    /**
     * Determine the current framework is Lumen
     *
     * @return bool
     */
    function is_lumen()
    {
        return file_exists(base_path('vendor/laravel/lumen-framework/readme.md'));
    }
}

if (! function_exists('is_51')) {
    /**
     * Determine if the current version of framework is based on 5.1
     *
     * @return bool
     */
    function is_51()
    {
        return str_contains(app()->version(), '5.1');
    }
}

if (! function_exists('is_api_request')) {
    /**
     * Determine if the current request is generated from an api client
     *
     * @return mixed
     */
    function is_api_request()
    {
        return starts_with(app('request')->getHttpHost(), config('fractal.domain'))
            or app('request')->is(config('fractal.pattern'))
            or app('request')->ajax();
    }
}

if (! function_exists('is_update_request')) {
    /**
     * Determine if the request is for update
     *
     * @return bool
     */
    function is_update_request()
    {
        $needle = ['put', 'patch'];

        return in_array(strtolower(app('request')->input('_method')), $needle)
        or in_array(strtolower(app('request')->header('x-http-method-override')), $needle);
    }
}

if (! function_exists('is_delete_request')) {
    /**
     * Determine if the request is for delete
     *
     * @return bool
     */
    function is_delete_request()
    {
        $needle = 'delete';

        return strtolower(app('request')->input('_method')) == $needle
        or strtolower(app('request')->header('x-http-method-override')) == $needle;
    }
}

if (! function_exists('json')) {
    /**
     * Instantiate Response class or make a json response.
     *
     * @param array $content
     * @return \Appkr\Fractal\Http\Response|\Illuminate\Http\JsonResponse
     */
    function json($content = [])
    {
        $factory = app(\Appkr\Fractal\Http\Response::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->respond($content);
    }
}
