<?php

namespace Appkr\Api\Example;

use App\Http\Controllers\Controller;
use Teapot\StatusCode;

class AuthorsController extends Controller
{
    /**
     * @var \Appkr\Api\Example\Author
     */
    private $model;

    /**
     * @param \Appkr\Api\Example\Author $model
     */
    public function __construct(Author $model)
    {
        $this->model = $model;
        $this->meta = [
            'version'       => 1,
            'documentation' => route('v1.doc'),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Http\Response
     */
    public function index()
    {
        return json()->setMeta($this->meta)->withPagination(
            $this->model->with('books')->latest()->paginate(5),
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
        return json()->setMeta($this->meta)->withItem(
            $this->model->findOrFail($id),
            new AuthorTransformer
        );
    }
}
