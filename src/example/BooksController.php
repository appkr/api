<?php

namespace Appkr\Api\Example;

use App\Http\Controllers\Controller;
use Appkr\Api\Http\Response;
use Teapot\StatusCode;

class BooksController extends Controller
{
    /**
     * @var \Appkr\Api\Example\Book
     */
    private $model;

    /**
     * @param \Appkr\Api\Example\Book $model
     */
    public function __construct(Book $model)
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
     * @param null $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function index($id = null)
    {
        // process v1.authors.books route
        if ('v1.authors.books' === \Route::currentRouteName()) {
            return json()->setMeta($this->meta)->withPagination(
                Author::find($id)->books()->latest()->paginate(5),
                new BookTransformer
            );
        }

        // Respond with pagination
        return json()->setMeta($this->meta)->withPagination(
            $this->model->latest()->paginate(5),
            new BookTransformer
        );

        // Respond as a collection
        //return json()->setMeta($this->meta)->withCollection(
        //    $this->model->latest()->get(),
        //    new BookTransformer
        //);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param BooksRequest $request
     * @return \Illuminate\Contracts\Http\Response
     */
    public function store(BooksRequest $request)
    {
        // Merging author_id for Demo purpose.
        // In real project we should use $request->user()->id instead.
        $data = array_merge(
            $request->all(),
            ['author_id' => 1]
        );

        if (! $book = $this->model->create($data)) {
            return json()->internalError('Failed to create !');
        }

        // respond created item with 201 status code and location header
        return json()
            ->setStatusCode(StatusCode::CREATED)
            ->setHeaders([
                'Location' => route('v1.books.show', ['id' => $book->id, 'include' => 'author']),
            ])
            ->withItem(
                $book,
                new BookTransformer
            );

        // respond simple message with 201 status code
        //return json()->created('Created');

        // You can also pass Eloquent model to the created method
        // Then it will append created resource to the response body
        //return json()->created($book);
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
            new BookTransformer
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Appkr\Api\Example\BooksRequest $request
     * @param int                              $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function update(BooksRequest $request, $id)
    {
        $book = $this->model->findOrFail($id);

        if (! $book->update($request->all())) {
            return json()->internalError('Failed to update !');
        }

        return json()->success('Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param BooksRequest $request
     * @param  int          $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function destroy(BooksRequest $request, $id)
    {
        $book = $this->model->findOrFail($id);

        if (! $book->delete()) {
            return json()->internalError('Failed to delete !');
        }

        return json()->success('Deleted');
    }
}
