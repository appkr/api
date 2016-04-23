    /**
     * Include {{ $include->relationship }}.
     *
     * @param \{{ $subject->model }} ${{ $subject->object }}
     * @param \League\Fractal\ParamBag|null $params
     * @return \League\Fractal\Resource\Item
     */
    public function {{ $include->method }}({{ $subject->basename }} ${{ $subject->object }}, ParamBag $params = null)
    {
        return $this->item(
            ${{ $subject->object }}->{{ $include->relationship }},
            new {{ $include->transformer }}($params)
        );
    }
