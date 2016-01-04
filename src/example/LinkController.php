<?php

namespace Appkr\Api\Example;

use App\Http\Controllers\Controller;
use Appkr\Api\Http\Response;

class LinkController extends Controller
{
    /**
     * Exposure a listing of the endpoints.
     *
     * @param \Appkr\Api\Http\Response $response
     * @return \Illuminate\Contracts\Http\Response
     */
    public function index(Response $response)
    {
        $payload = [
            'resources' => route('v1.books.index'),
            'authors'   => route('v1.authors.index'),
        ];

        return $response->setMeta([
            'message'       => "Hello, I'm a appkr/api example",
            'version'       => 1,
            'documentation' => route('v1.doc'),
        ])
            ->respond([
                'link' => $payload,
            ]);
    }
}
