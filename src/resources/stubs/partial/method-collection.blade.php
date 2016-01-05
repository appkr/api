    /**
     * Include {{ $include->relationship }}.
     *
     * @param \{{ $subject->model }} ${{ $subject->object }}
     * @param \League\Fractal\ParamBag|null $params
     * @return \League\Fractal\Resource\Collection
     */
    public function {{ $include->method }}({{ $subject->basename }} ${{ $subject->object }}, ParamBag $params = null)
    {
        list($limit, $offset, $orderCol, $orderBy) = $this->calculateParams($params);

        ${{ $include->relationship }} = ${{ $subject->object }}->{{ $include->relationship }}()->limit($limit)->offset($offset)->orderBy($orderCol, $orderBy)->get();

        return $this->collection(${{ $include->relationship }}, new {{ $include->transformer }});
    }