<?php

namespace Appkr\Fractal\Example;

use App\Http\Controllers\Controller;
use Appkr\Fractal\Http\Response;

class ThingsController extends Controller
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
        $this->meta    = [
            'version'       => 1,
            'documentation' => route('v1.doc')
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
        // process v1.authors.things route
        if ('v1.authors.things' === \Route::currentRouteName()) {
            return $this->respond->setMeta($this->meta)->withPagination(
                Author::find($id)->things()->latest()->paginate(25),
                new ThingTransformer
            );
        }

        // Respond with pagination
        return $this->respond->setMeta($this->meta)->withPagination(
            $this->model->latest()->paginate(25),
            new ThingTransformer
        );

        // Respond as a collection
        //return $this->respond->setMeta($this->meta)->withCollection(
        //    $this->model->latest()->get(),
        //    new ThingTransformer
        //);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ThingsRequest $request
     * @return \Illuminate\Contracts\Http\Response
     */
    public function store(ThingsRequest $request)
    {
        // Merging author_id. In real project
        // we should use $request->user()->id instead.
        $data = array_merge(
            $request->all(),
            ['author_id' => 1]
        );

        if (! $thing = $this->model->create($data)) {
            return $this->respond->internalError('Failed to create !');
        }

        // respond created item with 201 status code and location header
        return $this->respond
            ->setStatusCode(201)
            ->setHeaders([
                'Location' => route('v1.things.show', ['id' => $thing->id, 'include' => 'author'])
            ])
            ->withItem(
                $thing,
                new ThingTransformer
            );

        // respond simple message with 201 status code
        //return $this->respond->created('Created');

        // You can also pass Eloquent model to the created method
        // Then it will append created resource to the response body
        //return $this->respond->created($thing);
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
            $this->model->findOrFail($id),
            new ThingTransformer
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Appkr\Fractal\Example\ThingsRequest $request
     * @param int                                  $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function update(ThingsRequest $request, $id)
    {
        $thing = $this->model->findOrFail($id);

        if (! $thing->update($request->all())) {
            return $this->respond->internalError('Failed to update !');
        }

        return $this->respond->success('Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ThingsRequest $request
     * @param  int          $id
     * @return \Illuminate\Contracts\Http\Response
     */
    public function destroy(ThingsRequest $request, $id)
    {
        $thing = $this->model->findOrFail($id);

        if (! $thing->delete()) {
            return $this->respond->internalError('Failed to delete !');
        }

        return $this->respond->success('Deleted');
    }
}
