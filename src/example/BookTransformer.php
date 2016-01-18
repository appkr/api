<?php

namespace Appkr\Api\Example;

use Appkr\Api\TransformerAbstract;
use League\Fractal\ParamBag;

class BookTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected $availableIncludes = [
        'author',
    ];

    /**
     * List of resources to automatically include.
     *
     * @var array
     */
    //protected $defaultIncludes = [
    //    'author'
    //];

    /**
     * Transform single resource.
     *
     * @param \Appkr\Api\Example\Book $book
     * @return array
     */
    public function transform(Book $book)
    {
        $payload = [
            'id'           => (int) $book->id,
            'title'        => $book->title,
            'description'  => $book->description,
            'out_of_print' => (bool) $book->out_of_print == 1,
            'published_yr' => property_exists($book, 'published_at') ? $book->published_at->format('Y') : 'unknown',
            'link'         => [
                'rel'  => 'self',
                'href' => route('v1.books.show', [
                    'id'      => $book->id,
                    'include' => 'author',
                ]),
            ],
            'author'       => $book->author->name,
        ];

        if ($fields = $this->getPartialFields()) {
            $payload = array_only($payload, $fields);
        }

        return $payload;
    }

    /**
     * Include Author.
     *
     * @param \Appkr\Api\Example\Book  $book
     * @param \League\Fractal\ParamBag $params
     * @return \League\Fractal\Resource\Item
     */
    public function includeAuthor(Book $book, ParamBag $params = null)
    {
        return $this->item($book->author, new AuthorTransformer($params));
    }
}
