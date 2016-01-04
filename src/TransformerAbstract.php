<?php

namespace Appkr\Api;

use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract as FractalTransformer;

abstract class TransformerAbstract extends FractalTransformer
{
    /**
     * Calculate limit and offset modifiers out of the give params.
     * When consume the return value of this method, the ORDER matters.
     *
     * @param \League\Fractal\ParamBag|null $params
     * @return array [$limit, $offset, $orderCol, $orderBy]
     * @throws \Exception
     */
    protected function calculateParams(ParamBag $params = null)
    {
        if ($params === null) {
            // Temporarily work-around for https://github.com/thephpleague/fractal/issues/250
            $params = new ParamBag(config('api.include.params'));
        } else {
            $this->validateParams($params);
        }

        return array_merge(
            $params->get('limit') ?: config('api.include.params.limit'),
            $params->get('order') ?: config('api.include.params.order')
        );
    }

    /**
     * Validate include params.
     * We already define the white lists in the config.
     *
     * @param \League\Fractal\ParamBag $params
     * @throws \Exception
     */
    protected function validateParams(ParamBag $params)
    {
        $validParams = array_keys(config('api.include.params'));

        $usedParams = array_keys(iterator_to_array($params));

        if ($invalidParams = array_diff($usedParams, $validParams)) {
            throw new \Exception(sprintf('Invalid param(s): "%s". Valid param(s): "%s"', implode(', ', $usedParams), implode(', ', $validParams)));
        }
    }
}
