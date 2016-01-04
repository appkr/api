<?php

namespace Appkr\Api\Http;

class ResponseFactory
{
    /**
     * Create an instance of Laravel or Lumen ResponseFactory
     *
     * @return \Laravel\Lumen\Http\ResponseFactory
     */
    public function make()
    {
        if (is_lumen()) {
            return new \Laravel\Lumen\Http\ResponseFactory();
        }

        return app(\Illuminate\Contracts\Routing\ResponseFactory::class);
    }
}