<?php

namespace Appkr\Api;

use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract as FractalTransformer;

abstract class TransformerAbstract extends FractalTransformer
{
    /**
     * @var \League\Fractal\ParamBag
     */
    protected $params;

    /**
     * @var array
     */
    protected $parsed;

    public function __construct(ParamBag $params = null)
    {
        $this->params = $params;

        if ($this->params === null) {
            // Temporary work-around
            // @see https://github.com/thephpleague/fractal/issues/250
            $this->params = new ParamBag(config('api.include.params'));
        }

        $this->validateParams();

        $this->parse();
    }

    /**
     * Helper to enhance readability.
     *
     * @param string|null $key
     * @return array|null|string
     */
    public function getParsedParams($key = null)
    {
        return $this->get($key);
    }

    /**
     * Get the parsed value for the given key.
     *
     * @param string|null $key
     * @return array|null|string
     * @throws \Exception
     */
    public function get($key = null)
    {
        if (empty($this->parsed)) {
            return null;
        }

        if (is_null($key)) {
            return $this->parsed;
        }

        $allowedKeys = ['limit', 'offset', 'sort', 'order', config('api.partial.key')];

        if (! in_array($key, $allowedKeys)) {
            throw new \Exception(sprintf('Invalid key: "%s". Valid key(s): "%s"', $key, implode(', ', $allowedKeys)));
        }

        return $this->parsed[$key];
    }

    /**
     * Parse the given ParamBag and merge them with default values.
     *
     * @return array
     */
    protected function parse()
    {
        $config = config('api.include.params');
        $partialKey = config('api.partial.key');

        $limit  = $this->params->get('limit') ?: $config['limit'];
        $sort   = $this->params->get('sort') ?: $config['sort'];
        $fields = $this->params->get($partialKey);

        $this->parsed = [
            'limit'  => $limit[0],
            'offset' => $limit[1],
            'sort'   => $sort[0],
            'order'  => $sort[1],
            $partialKey => $fields,
        ];
    }

    /**
     * Validate include params.
     * We already define the white lists in the config.
     *
     * @return bool
     * @throws \Exception
     */
    protected function validateParams()
    {
        $validParams = array_merge(
            array_keys(config('api.include.params')),
            [config('api.partial.key')]
        );

        $usedParams = array_keys(iterator_to_array($this->params));

        if ($invalidParams = array_diff($usedParams, $validParams)) {
            throw new \Exception(sprintf('Invalid param(s): "%s". Valid param(s): "%s"', implode(', ', $usedParams), implode(', ', $validParams)));
        }

        return true;
    }

    /**
     * Calculate the list of fields for partial response.
     *
     * @return array
     */
    protected function getPartialFields()
    {
        $partialKey = config('api.partial.key');

        if ($fields = $this->get($partialKey)) {
            return $fields;
        }

        if ($fields = request()->input($partialKey)) {
            return explode(',', $fields);
        }

        return [];
    }
}
