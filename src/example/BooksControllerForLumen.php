<?php

/**
 * Lumen doesn't have FormRequest feature.
 * That's the only reason why we separated Laravel and Lumen controller.
 */

namespace Appkr\Api\Example;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BooksControllerForLumen extends Controller
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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Http\Response
     */
    public function index()
    {
        // Respond with pagination
        return json()->setMeta(['version' => 1])->withPagination(
            $this->model->with('author')->latest()->paginate(5),
            new BookTransformer
        );

        // Respond as a collection
        //return json()->setMeta(['version' => 1])->withCollection(
        //    $this->model->with('author')->latest()->get(),
        //    new BookTransformer
        //);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return $this|\Illuminate\Contracts\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required|min:2',
            'description' => 'min:2',
        ]);

        // Merging author_id for Demo purpose.
        // In real project we should use $request->user()->id instead.
        $data = array_merge(
            $request->all(),
            ['author_id' => 1]
        );

        if (! $book = Book::create($data)) {
            return json()->internalError('Failed to create !');
        }

        // respond created item with 201 status code
        return json()->setStatusCode(201)->withItem(
            $book,
            new BookTransformer
        );

        // respond with simple message
        //return json()->created('Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function show($id)
    {
        return json()->setMeta(['version' => 1])->withItem(
            $this->model->findOrFail($id),
            new BookTransformer
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title'       => 'required|min:2',
            'description' => 'min:2',
            'deprecated'  => 'boolean',
        ]);

        $book = $this->model->findOrFail($id);

        if (! $book->update($request->all())) {
            return json()->internalError('Failed to update !');
        }

        return json()->success('Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int                     $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $book = $this->model->findOrFail($id);

        if (! $book->delete()) {
            return json()->internalError('Failed to delete !');
        }

        return json()->success('Deleted');
    }
}
