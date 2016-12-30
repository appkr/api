<?php

namespace Appkr\Api\Transformers;

use Illuminate\Support\Collection;
use JsonSerializable;
use League\Fractal\TransformerAbstract;

class SimpleArrayTransformer extends TransformerAbstract
{
    /**
     * Transform single resource
     *
     * @param Collection|array $model
     * @return array
     * @throws \Exception
     */
    public function transform($model)
    {
        if (is_array($model)) {
            return $model;
        }

        if ($model instanceof Collection) {
            return $model->toArray();
        }

        if ($model instanceof JsonSerializable) {
            return $model->jsonSerialize();
        }

        if ($model instanceof \stdClass) {
            return (array) $model;
        }

        throw new \Exception(
            'Expecting an instance of \Illuminate\Support\Collection, '
            . get_class($model)
            . ' given.'
        );
    }
}
