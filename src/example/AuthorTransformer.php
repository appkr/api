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
     * List of resources to automatically include.
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * List of attributes to respond.
     *
     * @var array
     */
    protected $visible = [];

    /**
     * List of attributes NOT to respond.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Transform single resource.
     *
     * @param \Appkr\Api\Example\Author $author
     * @return array
     */
    public function transform(Author $author)
    {
        $payload = [
            'id' => (int) $author->id,
            'name' => $author->name,
            'email' => $author->email,
            'created_at' => $author->created_at->toIso8601String(),
            'link' => [
                'rel' => 'self',
                'href' => route('v1.authors.show', [
                    'id' => $author->id,
                    'include' => 'books',
                ]),
            ],
            'books' => (int) $author->books->count(),
        ];

        return $this->buildPayload($payload);
    }

    /**
     * Include books.
     *
     * @param \Appkr\Api\Example\Author $author
     * @param \League\Fractal\ParamBag|null $paramBag
     * @return \League\Fractal\Resource\Collection
     */
    public function includeBooks(Author $author, ParamBag $paramBag = null)
    {
        $transformer = new BookTransformer($paramBag);

        $books = $author->books()
            ->limit($transformer->getLimit())
            ->offset($transformer->getOffset())
            ->orderBy($transformer->getSortKey(), $transformer->getSortDirection())
            ->get();

        return $this->collection($books, new BookTransformer);
    }
}
