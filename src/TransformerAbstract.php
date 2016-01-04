<?php

namespace Appkr\Api;

use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract as FractalTransformer;

abstract class TransformerAbstract extends FractalTransformer
{
    protected function validateParams(ParamBag $params)
    {
        // params validation
        $validParams = config('api.include.params');

        $usedParams = array_keys(iterator_to_array($params));

        if ($invalidParams = array_diff($usedParams, $validParams)) {
            throw new \Exception(sprintf('Invalid param(s): "%s". Valid param(s): "%s"', implode(', ', $usedParams), implode(', ', $validParams)));
        }
    }
}
