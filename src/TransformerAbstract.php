<?php

namespace Appkr\Api;

use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract as FractalTransformer;
use UnexpectedValueException;

abstract class TransformerAbstract extends FractalTransformer
{
    /**
     * Number of items when the resource is being included.
     *
     * @var int
     */
    protected $limit;

    /**
     * Number of skip over when the resource is being includes.
     *
     * @var int
     */
    protected $offset;

    /**
     * Name of attributes used as sort key.
     *
     * @var string
     */
    protected $sortKey;

    /**
     * Sort direction. Should be 'asc' or 'desc'.
     *
     * @var int
     */
    protected $sortDirection;

    /**
     * List of attributes to respond.
     * Note that the value of this property must the mapped key.
     * Mapped key may differ from the model attributes.
     *
     * @var array
     */
    protected $visible = [];

    /**
     * List of attributes NOT to respond.
     * Note that the value of this property must the mapped key.
     * Mapped key may differ from the model attributes.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @var ParamBag
     */
    protected $paramBag;

    /**
     * @var array
     */
    protected $config;

    /**
     * TransformerAbstract constructor.
     *
     * @param ParamBag|null $paramBag
     */
    public function __construct(ParamBag $paramBag = null)
    {
        $this->paramBag = $paramBag;
        $this->config = app('config')->get('api');

        if ($this->paramBag === null) {
            // Temporary work-around
            // @see https://github.com/thephpleague/fractal/issues/250
            $this->paramBag = new ParamBag($this->config['include']['params']);
        }

        $this->validateIncludeParams();

        $this->setProperties();
    }

    /* GETTERS */

    /** @return int */
    public function getLimit()
    {
        if (! $this->limit) {
            return $this->config['include']['params']['limit'][0];
        }

        return $this->limit;
    }

    /** @return int */
    public function getOffset()
    {
        if (! $this->offset) {
            return $this->config['include']['params']['limit'][1];
        }

        return $this->offset;
    }

    /** @return string */
    public function getSortKey()
    {
        if (! $this->sortKey) {
            return $this->config['include']['params']['sort'][0];
        }

        return $this->sortKey;
    }

    /** @return int */
    public function getSortDirection()
    {
        if (! $this->sortDirection) {
            return $this->config['include']['params']['sort'][1];
        }

        return $this->sortDirection;
    }

    /** @return array */
    public function getVisible()
    {
        return $this->visible;
    }

    /** @return array */
    public function getHidden()
    {
        return $this->hidden;
    }

    /** @return array */
    public function getFields()
    {
        return array_unique(array_diff($this->visible, $this->hidden));
    }

    /* SETTERS */

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $sortKey
     * @return $this
     */
    public function setSortKey($sortKey)
    {
        $this->sortKey = $sortKey;

        return $this;
    }

    /**
     * @param string $sortDirection
     * @return $this
     */
    public function setSortDirection($sortDirection)
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setVisible(array $attributes)
    {
        $this->visible = $attributes;

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setHidden(array $attributes)
    {
        $this->hidden = $attributes;

        return $this;
    }

    protected function setProperties()
    {
        // Fetch request query string values passed by an API client.
        list($limit, $offset) = $this->paramBag->get('limit');
        list($sortKey, $sortDirection) = $this->paramBag->get('sort');

        // If nothing is passed by API client,
        // falling back to class property's value.
        $this->limit = $limit ?: $this->limit;
        $this->offset = $offset ?: $this->offset;
        $this->sortKey = $sortKey ?: $this->sortKey;
        $this->sortDirection = $sortDirection ?: $this->sortDirection;
    }

    /**
     * Validate include params.
     * We already define the white lists in the config.
     *
     * @return bool
     * @throws \UnexpectedValueException
     */
    protected function validateIncludeParams()
    {
        $validParams = array_keys($this->config['include']['params']);
        $usedParams = array_keys(iterator_to_array($this->paramBag));

        if ($invalidParams = array_diff($usedParams, $validParams)) {
            // This validates query string KEY passed by an API client.
            throw new UnexpectedValueException(
                sprintf(
                    'Used param(s): "%s". Valid param(s): "%s"',
                    implode(',', $usedParams),
                    implode(',', $validParams)
                )
            );
        }

        $errors = [];

        if ($limit = $this->paramBag->get('limit')) {
            if (count($limit) !== 2) {
                array_push(
                    $errors,
                    'Invalid "limit" value. Valid usage: limit(int|int) where the first int is number of items to retrieve and the second is offset to skip over.'
                );
            }

            foreach($limit as $item) {
                if (! is_numeric($item)) {
                    array_push(
                        $errors,
                        'Invalid "limit" value. Expecting: integer. Given: ' . gettype($item) . " \"{$item}\"."
                    );
                }
            }
        }

        if ($sort = $this->paramBag->get('sort')) {
            if (count($sort) !== 2) {
                array_push(
                    $errors,
                    'Invalid "sort" value. Valid usage: sort(string|string) where the first string is attribute name to order by and the second is the sort direction(asc or desc)'
                );
            }

            $allowedSortDirection = ['asc', 'desc'];

            if (isset($sort[1]) && ! in_array(strtolower($sort[1]), $allowedSortDirection)) {
                array_push(
                    $errors,
                    'Invalid "sort" value. Allowed: ' . implode(',', $allowedSortDirection) . ". Given: \"{$sort[1]}\""
                );
            }
        }

        if (! empty($errors)) {
            throw new UnexpectedValueException(implode(PHP_EOL, $errors));
        }

        return true;
    }
}
