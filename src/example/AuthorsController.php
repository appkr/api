<?php

namespace Appkr\Fractal\Example;

use App\Http\Controllers\Controller;
use Appkr\Fractal\Http\Response;

class AuthorsController extends Controller
{
    /**
     * @var \Appkr\Fractal\Example\Author
     */
    private $model;

    /**
     * @var \Appkr\Fractal\Http\Response
     */
    private $respond;

    /**
     * @param \Appkr\Fractal\Example\Author $model
     * @param \Appkr\Fractal\Http\Response  $respond
     */
    public function __construct(Author $model, Response $respond)
    {
        $this->model   = $model;
        $this->respond = $respond;
        $this->meta    = [
            'version'       => 1,
            'documentation' => route('v1.doc')
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Http\Response
     */
    public function index()
    {
        return $this->respond->setMeta($this->meta)->withPagination(
            $this->model->latest()->paginate(5),
            new AuthorTransformer
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function show($id)
    {
        return $this->respond->setMeta($this->meta)->withItem(
            $this->model->with('things')->findOrFail($id),
            new AuthorTransformer
        );
    }
}
