<?php

if (! function_exists('snake_case')) {
    function snake_case($string)
    {
        return \Illuminate\Support\Str::snake($string);
    }
}

if (! function_exists('str_contains')) {
    function str_contains($string)
    {
        return \Illuminate\Support\Str::contains($string);
    }
}

if (! function_exists('str_singular')) {
    function str_singular($string)
    {
        return \Illuminate\Support\Str::singular($string);
    }
}

if (! function_exists('str_plural')) {
    function str_plural($string)
    {
        return \Illuminate\Support\Str::plural($string);
    }
}

if (! function_exists('starts_with')) {
    function starts_with($string)
    {
        return \Illuminate\Support\Str::startsWith($string);
    }
}

if (! function_exists('is_laravel')) {
    /**
     * Determine the current framework is Laravel
     *
     * @return bool
     */
    function is_laravel()
    {
        return app() instanceof Illuminate\Foundation\Application;
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
        return app() instanceof Laravel\Lumen\Application;
    }
}

if (! function_exists('is_50')) {
    /**
     * Determine if the current version of framework is based on 5.0.*
     *
     * @return bool
     */
    function is_50()
    {
        return str_contains(app()->version(), '5.0');
    }
}

if (! function_exists('is_51')) {
    /**
     * Determine if the current version of framework is based on 5.1.*
     *
     * @return bool
     */
    function is_51()
    {
        return str_contains(app()->version(), '5.1');
    }
}

if (! function_exists('is_52')) {
    /**
     * Determine if the current version of framework is based on 5.2.*
     *
     * @return bool
     */
    function is_52()
    {
        return str_contains(app()->version(), '5.2');
    }
}

if (! function_exists('is_api_request')) {
    /**
     * Determine if the current request is for API endpoints, and expecting API response
     *
     * @return mixed
     */
    function is_api_request()
    {
        return starts_with(app('request')->getHttpHost(), config('api.endpoint.domain'))
            or app('request')->is(config('api.endpoint.pattern'))
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
            or in_array(strtolower(app('request')->header('x-http-method-override')), $needle)
            or in_array(strtolower(app('request')->method()), $needle);
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
            or strtolower(app('request')->header('x-http-method-override')) == $needle
            or strtolower(app('request')->method()) == $needle;
    }
}

if (! function_exists('json')) {
    /**
     * Instantiate a Response class or make a json response.
     *
     * @param array $content
     * @return \Appkr\Api\Http\Response|\Illuminate\Http\JsonResponse
     */
    function json($content = [])
    {
        $factory = app(\Appkr\Api\Http\Response::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->respond($content);
    }
}
