<?php

namespace Appkr\Api\Example;

use Appkr\Api\TransformerAbstract;

class AuthorTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'books',
    ];

    /**
     * Transform single resource
     *
     * @param \Appkr\Api\Example\Author $author
     * @return array
     */
    public function transform(Author $author)
    {
        return [
            'id'         => (int)$author->id,
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
            'books'      => (int)$author->books->count(),
        ];
    }

    /**
     * Include books.
     *
     * @param \Appkr\Api\Example\Author     $author
     * @param \League\Fractal\ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|null
     * @throws \Exception
     */
    public function includeBooks(Author $author, $params = null)
    {
        if ($params) {
            $this->validateParams($params);
        }

        list($limit, $offset) = $params['limit'] ?: config('api.include.limit');
        list($orderCol, $orderBy) = $params['order'] ?: config('api.include.order');

        $books = $author->books()->limit($limit)->offset($offset)->orderBy($orderCol, $orderBy)->get();

        return $this->collection($books, new BookTransformer);
    }
}
