<?php

namespace Appkr\Fractal\Commands;

class ArgumentConverter
{
    /**
     * String calculation based on the command line argument.
     * e.g. App\Book
     *
     * @param  string $subject
     * @return array
     */
    public function convert($subject)
    {
        $fqcn = starts_with('\\', $subject) ? $subject : '\\' . $subject;

        return [
            'fqcn' => $fqcn,
            'model' => ltrim($fqcn, '\\'),
            'basename' => class_basename($fqcn),
            'object' => str_singular(strtolower(class_basename($fqcn))),
            'transformer' => ucfirst(class_basename($fqcn)) . 'Transformer',
            'route' => str_plural(strtolower(class_basename($fqcn))) . '.show',
        ];
    }
}