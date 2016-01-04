<?php

namespace Appkr\Api\Commands;

class ArgumentConverter
{
    /**
     * String calculation based on the given string which is a command line argument.
     * e.g. App\\Book
     *
     * @param  string $subject
     * @return \stdClass
     */
    public function convert($subject)
    {
        $fqcn = starts_with('\\', $subject) ? $subject : '\\' . $subject;

        $obj = new \stdClass;

        $obj->fqcn = $fqcn;
        $obj->model = ltrim($fqcn, '\\');
        $obj->basename = class_basename($fqcn);
        $obj->object = str_singular(strtolower($obj->basename));
        $obj->transformer = ucfirst($obj->basename) . 'Transformer';
        $obj->route = 'api.v1.' . str_plural(strtolower($obj->basename)) . '.show';

        return $obj;
    }
}