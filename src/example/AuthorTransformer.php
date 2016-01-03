<?php

namespace Appkr\Fractal\Example;

use League\Fractal;
use League\Fractal\TransformerAbstract;

class AuthorTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'things'
    ];

    /**
     * Transform single resource
     *
     * @param \Appkr\Fractal\Example\Author $author
     * @return array
     */
    public function transform(Author $author)
    {
        return [
            'id'         => (int) $author->id,
            'name'       => $author->name,
            'email'      => $author->email,
            //'created_at' => (int) $author->created_at->getTimestamp(),
            'created_at' => $author->created_at->toIso8601String(),
            'link'       => [
                'rel'  => 'self',
                'href' => route('v1.authors.show', [
                    'id'      => $author->id,
                    'include' => 'things'
                ])
            ],
            'things'     => (int) $author->things->count()
        ];
    }

    /**
     * Include Thing
     *
     * @param \Appkr\Fractal\Example\Author $author
     * @return \League\Fractal\Resource\Collection|null
     */
    public function includeThings(Author $author)
    {
        $things = $author->things;

        return $things
            ? $this->collection($things, new ThingTransformer)
            : null;
    }
}
