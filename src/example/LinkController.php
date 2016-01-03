<?php

namespace Appkr\Fractal\Example;

use App\Http\Controllers\Controller;
use Appkr\Fractal\Http\Response;

class LinkController extends Controller
{
    /**
     * Exposure a listing of the endpoints.
     *
     * @param \Appkr\Fractal\Http\Response $response
     * @return \Illuminate\Contracts\Http\Response
     */
    public function index(Response $response)
    {
        $payload = [
            'resources' => route('v1.things.index'),
            'authors'   => route('v1.authors.index')
        ];

        return $response->setMeta([
            'message'       => "Hello, I'm a appkr/fractal example api",
            'version'       => 1,
            'documentation' => route('v1.doc')
        ])
            ->respond([
                'link' => $payload
            ]);
    }
}
