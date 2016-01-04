<?php

namespace Appkr\Api\Http;

trait ApiResponse
{
    /**
     * Get a Response instance
     *
     * @return \Appkr\Api\Http\Response
     */
    public function response()
    {
        return app(Response::class);
    }

    /**
     * Get a Response instance
     *
     * @return \Appkr\Api\Http\Response
     */
    public function respond()
    {
        return app(Response::class);
    }
}