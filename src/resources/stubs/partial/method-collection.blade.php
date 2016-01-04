    /**
     * Include {{ $include->relationship }}.
     *
     * @param \{{ $subject->model }} ${{ $subject->object }}
     * @param \League\Fractal\ParamBag|null $params
     * @return \League\Fractal\Resource\Collection
     * @throws \Exception
     */
    public function {{ $include->method }}({{ $subject->basename }} ${{ $subject->object }}, $params)
    {
        if ($params) {
            $this->validateParams($params);
        }

        list($limit, $offset) = $params->get('limit') ?: config('api.include.limit');
        list($orderCol, $orderBy) = $params->get('order') ?: config('api.include.order');

        ${{ $include->relationship }} = ${{ $subject->object }}->{{ $include->relationship }}()->limit($limit)->offset($offset)->orderBy($orderCol, $orderBy)->get();

        return $this->collection(${{ $include->relationship }}, new {{ $include->transformer }});
    }