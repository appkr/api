<?php

namespace Appkr\Fractal\Example;

use App\Http\Controllers\Controller;
use Appkr\Fractal\Http\Response;
use Illuminate\Http\Request;

class ThingsControllerForLumen extends Controller
{
    /**
     * @var \Appkr\Fractal\Example\Thing
     */
    private $model;

    /**
     * @var \Appkr\Fractal\Http\Response
     */
    private $respond;

    /**
     * @param \Appkr\Fractal\Example\Thing $model
     * @param \Appkr\Fractal\Http\Response $respond
     */
    public function __construct(Thing $model, Response $respond)
    {
        $this->model   = $model;
        $this->respond = $respond;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Http\Response
     */
    public function index()
    {
        // Respond with pagination
        return $this->respond->setMeta(['version' => 1])->withPagination(
            $this->model->with('author')->latest()->paginate(25),
            new ThingTransformer
        );

        // Respond as a collection
        //return $this->respond->setMeta(['version' => 1])->withCollection(
        //    $this->model->with('author')->latest()->get(),
        //    new ThingTransformer
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
            'description' => 'min:2'
        ]);

        // Merging author_id. In real project
        // we should use $request->user()->id instead.
        $data = array_merge(
            $request->all(),
            ['author_id' => 1]
        );

        if (! $thing = Thing::create($data)) {
            return $this->respond->internalError('Failed to create !');
        }

        // respond created item with 201 status code
        return $this->respond->setStatusCode(201)->withItem(
            $thing,
            new ThingTransformer
        );

        // respond with simple message
        //return $this->respond->created('Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function show($id)
    {
        return $this->respond->setMeta(['version' => 1])->withItem(
            $this->model->findOrFail($id),
            new ThingTransformer
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
            'deprecated'  => 'boolean'
        ]);

        $thing = $this->model->findOrFail($id);

        if (! $thing->update($request->all())) {
            return $this->respond->internalError('Failed to update !');
        }

        return $this->respond->success('Updated');
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
        $thing = $this->model->findOrFail($id);

        if (! $thing->delete()) {
            return $this->respond->internalError('Failed to delete !');
        }

        return $this->respond->success('Deleted');
    }
}
