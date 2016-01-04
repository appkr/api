    /**
     * Include {{ $include->relationship }}.
     *
     * @param \{{ $subject->model }} ${{ $subject->object }}
     * @return \League\Fractal\Resource\Item
     */
    public function {{ $include->method }}({{ $subject->basename }} ${{ $subject->object }})
    {
        return $this->item(${{ $subject->object }}->{{ $include->relationship }}, new {{ $include->transformer }});
    }