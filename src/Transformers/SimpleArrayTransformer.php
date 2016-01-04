<?php

namespace Appkr\Api\Transformers;

use Illuminate\Database\Eloquent;
use League\Fractal;
use League\Fractal\TransformerAbstract;

class SimpleArrayTransformer extends TransformerAbstract
{
    /**
     * Transform single resource
     *
     * @param $model
     * @return array
     * @throws \Exception
     */
    public function transform($model)
    {
        if (! $model instanceof Collection::class) {
            throw new \Exception('Expecting an instance of \Illuminate\Database\Eloquent\Collection, ' . get_class($model) . ' given.');
        }

        return $model->toArray();
    }
}