<?php

namespace Appkr\Fractal\Http;

trait ApiResponse
{
    /**
     * Get a Response instance
     *
     * @return \Appkr\Fractal\Http\Response
     */
    public function response()
    {
        return app(Response::class);
    }

    /**
     * Get a Response instance
     *
     * @return \Appkr\Fractal\Http\Response
     */
    public function respond()
    {
        return app(Response::class);
    }
}