<?php

namespace Appkr\Api\Example;

use Appkr\Api\TransformerAbstract;
use League\Fractal\ParamBag;

class AuthorTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = [
        'books',
    ];

    /**
     * Transform single resource.
     *
     * @param \Appkr\Api\Example\Author $author
     * @return array
     */
    public function transform(Author $author)
    {
        $payload = [
            'id'         => (int) $author->id,
            'name'       => $author->name,
            'email'      => $author->email,
            //'created_at' => (int) $author->created_at->getTimestamp(),
            'created_at' => $author->created_at->toIso8601String(),
            'link'       => [
                'rel'  => 'self',
                'href' => route('v1.authors.show', [
                    'id'      => $author->id,
                    'include' => 'books',
                ]),
            ],
            'books'      => (int) $author->books->count(),
        ];

        if ($fields = $this->getPartialFields()) {
            $payload = array_only($payload, $fields);
        }

        return $payload;
    }

    /**
     * Include books.
     *
     * @param \Appkr\Api\Example\Author     $author
     * @param \League\Fractal\ParamBag|null $params
     * @return \League\Fractal\Resource\Collection
     */
    public function includeBooks(Author $author, ParamBag $params = null)
    {
        $transformer = new BookTransformer($params);

        $parsed = $transformer->getParsedParams();

        $books = $author->books()->limit($parsed['limit'])->offset($parsed['offset'])->orderBy($parsed['sort'], $parsed['order'])->get();

        return $this->collection($books, new BookTransformer);
    }
}
