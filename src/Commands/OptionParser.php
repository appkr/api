<?php

namespace Appkr\Fractal\Commands;

class OptionParser
{
    private $options = [];

    /**
     * Parse the command line option.
     * e.g. --includes=App\\User:author,App\\Comment:comments:true
     * If the third element is provided as true, yes, or 1,
     * the command will interpret the include as an collection.
     *
     * @param  string $raw
     * @return array
     */
    public function parse($raw)
    {
        if (empty($raw)) {
            return [];
        }

        $fields = $this->splitIntoFields($raw);

        foreach ($fields as $field) {
            $segments = $this->parseSegments($field);

            $this->addField($segments);
        }

        return $this->options;
    }

    /**
     * Get an array of fields from the given string.
     *
     * @param  string $raw
     * @return array
     */
    private function splitIntoFields($raw)
    {
        return preg_split('/,\s?(?![^()]*\))/', $raw);
    }

    /**
     * Get the segments of the option field.
     *
     * @param  string $field
     * @return array
     * @throws \Exception
     */
    private function parseSegments($field)
    {
        $field = starts_with('\\', $field) ? $field : '\\' . $field;

        $segments = explode(':', $field);

        if (count($segments) < 2) {
            throw new \Exception(
                '--includes option should be consist of string value of model, separated by colon(:),
                and string value of relationship. e.g. App\User:author, App\Comment:comments:true.
                If the third element is provided as true, yes, or 1, the command will interpret the include as an collection.'
            );
        }

        $fqcn = array_shift($segments);
        $relationship = array_shift($segments);
        $type = in_array(array_shift($segments), ['yes', 'y', 'true', true, '1', 1])
            ? 'collection' : 'item';
        $namespace = config('fractal.transformer.namespace');
        $namespace = (starts_with($namespace, '\\') ? $namespace : '\\' . $namespace);
        $namespace = (ends_with($namespace, '\\') ? $namespace : $namespace . '\\');

        return [
            'type' => $type,
            'fqcn' => $fqcn,
            'model' => ltrim($fqcn, '\\'),
            'basename' => class_basename($fqcn),
            'relationship' => $relationship,
            'method' => 'include' . ucfirst($relationship),
            'transformer' => $namespace . ucfirst(class_basename($fqcn)) . 'Transformer',
        ];
    }

    /**
     * Add a field to the schema array.
     *
     * @param  array $field
     * @return $this
     */
    private function addField($field)
    {
        $this->options[] = $field;

        return $this;
    }
}