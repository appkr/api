    /**
     * Include {{ $include->relationship }}.
     *
     * @param \{{ $subject->model }} ${{ $subject->object }}
     * @param \League\Fractal\ParamBag|null $paramBag
     * @return \League\Fractal\Resource\Collection
     */
    public function {{ $include->method }}({{ $subject->basename }} ${{ $subject->object }}, ParamBag $paramBag = null)
    {
        $transformer = new {{ $include->transformer }}($paramBag);

        ${{ $include->relationship }} = ${{ $subject->object }}->{{ $include->relationship }}()
            ->limit($transformer->getLimit())
            ->offset($transformer->getOffset())
            ->orderBy($transformer->getSortKey(), $transformer->getSortDirection())
            ->get();

        return $this->collection(${{ $include->relationship }}, $transformer);
    }
