    /**
     * Include {{ $include->relationship }}.
     *
     * @param \{{ $subject->model }} ${{ $subject->object }}
     * @param \League\Fractal\ParamBag|null $paramBag
     * @return \League\Fractal\Resource\Item
     */
    public function {{ $include->method }}({{ $subject->basename }} ${{ $subject->object }}, ParamBag $paramBag = null)
    {
        return $this->item(
            ${{ $subject->object }}->{{ $include->relationship }},
            new {{ $include->transformer }}($paramBag)
        );
    }
