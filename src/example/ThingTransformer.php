<?php

namespace Appkr\Fractal\Example;

use League\Fractal;
use League\Fractal\TransformerAbstract;

class ThingTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'author'
    ];

    /**
     * List of resources to automatically include
     *
     * @var array
     */
    //protected $defaultIncludes = [
    //    'author'
    //];

    /**
     * Transform single resource
     *
     * @param \Appkr\Fractal\Example\Thing $thing
     * @return array
     */
    public function transform(Thing $thing)
    {
        return [
            'id'          => (int) $thing->id,
            'title'       => $thing->title,
            'description' => $thing->description,
            'deprecated'  => (bool) ($thing->deprecated == 1) ? true : false,
            //'created_at'  => (int) $thing->created_at->getTimestamp(),
            'created_at'  => $thing->created_at->toIso8601String(),
            'link'        => [
                'rel'  => 'self',
                'href' => route('v1.things.show', [
                    'id'      => $thing->id,
                    'include' => 'author'
                ])
            ],
            'author'      => $thing->author->name
        ];
    }

    /**
     * Include Author
     *
     * @param \Appkr\Fractal\Example\Thing $thing
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeAuthor(Thing $thing)
    {
        $author = $thing->author;

        return $author
            ? $this->item($author, new AuthorTransformer)
            : null;
    }
}
