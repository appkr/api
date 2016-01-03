<?php

namespace Appkr\Fractal\Transformers;

use League\Fractal;
use League\Fractal\TransformerAbstract;

class SimpleArrayTransformer extends TransformerAbstract
{
    /**
     * Transform single resource
     *
     * @param $model
     *
     * @return array
     */
    public function transform($model)
    {
        return $model->toArray();
    }
}