<?php

namespace Appkr\Api\Support;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use stdClass;

class PayloadConverter
{
    public static function run($data)
    {
        $caseFunc = config('api.convert.key');
        $dateFormat = config('api.convert.date');
        $dateTimeStringRegex = '/^(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})$/';

        if ($dateFormat && $data instanceof Carbon) {
            return $data->format($dateFormat);
        }

        if ($data instanceof JsonSerializable && !is_array($data->jsonSerialize())) {
            return $data;
        }

        if ($data instanceof JsonSerializable && is_array($data->jsonSerialize())) {
            $data = $data->jsonSerialize();
        } elseif ($data instanceof Arrayable) {
            $data = $data->toArray();
        } elseif ($data instanceof stdClass) {
            $data = (array) $data;
        }

        if (! is_array($data)) {
            return $data;
        }

        // Converting field key
        $newData = [];
        foreach ($data as $key => $value) {
            $newKey = $caseFunc ? call_user_func($caseFunc, $key) : $key;

            if (is_string($value) && preg_match($dateTimeStringRegex, $value)) {
                $value = (new Carbon($value))->format($dateFormat);
            }

            $newData[$newKey] = static::run($value);
        }

        return $newData;
    }

    public function __invoke($data)
    {
        return static::run($data);
    }
}
