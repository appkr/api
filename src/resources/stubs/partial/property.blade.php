    /**
     * List of resources possible to include using url query string.
     * e.g. collection case -> ?include=comments:limit(5|1):order(created_at|desc)
     *      item case       -> ?include=author
     *
     * @var array
     */
    protected $availableIncludes = [{!! "'" . $includes->implode('relationship', "', '") ."'" !!}];

    /**
     * List of resources to include automatically/always.
     *
     * @var array
     */
    // protected $defaultIncludes = [{!! "'" . $includes->implode('relationship', "', '") ."'" !!}];