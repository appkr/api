    /**
     * Include {{ $include->relationship }}.
     *
     * @param \{{ $subject->model }} ${{ $subject->object }}
     * @param \League\Fractal\ParamBag|null $params
     * @return \League\Fractal\Resource\Collection
     */
    public function {{ $include->method }}({{ $subject->basename }} ${{ $subject->object }}, ParamBag $params = null)
    {
        $transformer = new {{ $include->transformer }}($params);

        $parsed = $transformer->getParsedParams();

        ${{ $include->relationship }} = ${{ $subject->object }}->{{ $include->relationship }}()
            ->limit($parsed['limit'])
            ->offset($parsed['offset'])
            ->orderBy($parsed['sort'], $parsed['order'])
            ->get();

        return $this->collection(${{ $include->relationship }}, $transformer);
    }
