<?php

namespace Appkr\Api\Example;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Teapot\StatusCode\All as StatusCode;

class BookApiTestForLumen extends \TestCase
{
    use DatabaseTransactions;

    /**
     * Stubbed Author model
     *
     * @var \Appkr\Api\Example\Author
     */
    protected $author;

    /**
     * Stubbed book
     *
     * @var array
     */
    protected $books = [];

    /**
     * JWT token
     *
     * @var string
     */
    protected $jwtToken = 'header.payload.signature';

    /** @before */
    public function stub()
    {
        $faker = \Faker\Factory::create();

        $this->author = \Appkr\Api\Example\Author::create([
            'name'  => 'foo',
            'email' => $faker->safeEmail,
        ]);

        $this->books = \Appkr\Api\Example\Book::create([
            'title'       => $faker->sentence(),
            'author_id'   => $this->author->id,
            'published_at'=> $faker->dateTimeThisCentury,
            'description' => $faker->randomElement([$faker->paragraph(), null]),
            'out_of_print'=> $faker->randomElement([0, 1]),
            'created_at'  => $faker->dateTimeThisYear,
        ])->toArray();
    }

    /** @test */
    public function it_fetches_a_collection_of_books()
    {
        $this->get('v1/books', $this->getHeaders())
            ->seeStatusCode(StatusCode::OK)
            ->seeJson();
    }

    /** @test */
    public function it_fetches_a_instance_of_book()
    {
        $this->get('v1/books/' . $this->books['id'], $this->getHeaders())
            ->seeStatusCode(StatusCode::OK)
            ->seeJson();
    }

    /** @test */
    public function it_throws_exception_if_requested_book_is_not_found()
    {
        $this->get('v1/books/100000', $this->getHeaders())
            ->getExpectedException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
    }

    /** @test */
    public function it_responds_422_if_create_book_request_fails_validation()
    {
        $payload = [
            'title'       => null,
            'author_id'   => null,
            'description' => 'n',
        ];

        $this->post('v1/books', $payload, $this->getHeaders())
            ->seeStatusCode(StatusCode::UNPROCESSABLE_ENTITY)
            ->seeJson();
    }

    /** @test */
    public function it_responds_201_after_creation()
    {
        $payload = [
            'title'       => 'new title',
            'author_id'   => $this->author->id,
            'description' => 'new description',
        ];

        $this->actingAs($this->author)
            ->post('v1/books', $payload, $this->getHeaders())
            ->seeInDatabase('books', ['title' => 'new title'])
            ->seeStatusCode(StatusCode::CREATED)
            ->seeJsonContains(['title' => 'new title']);
    }

    /** @test */
    public function it_responds_200_if_a_update_request_success()
    {
        $this->actingAs($this->author)
            ->put(
                'v1/books/' . $this->books['id'],
                ['title' => 'MODIFIED title', '_method' => 'PUT'],
                $this->getHeaders()
            )
            ->seeInDatabase('books', ['title' => 'MODIFIED title'])
            ->seeStatusCode(StatusCode::OK)
            ->seeJson();
    }

    /** @test */
    public function it_responds_200_if_a_delete_request_success()
    {
        $this->actingAs($this->author)
            ->delete(
                'v1/books/' . $this->books['id'],
                ['_method' => 'DELETE'],
                $this->getHeaders()
            )
            ->notSeeInDatabase('books', ['id' => $this->books['id']])
            ->seeStatusCode(StatusCode::OK)
            ->seeJson();
    }

    /**
     * Set/Get http request header
     *
     * @param array $append
     * @return array
     */
    protected function getHeaders($append = [])
    {
        return [
            'HTTP_Authorization' => "Bearer {$this->jwtToken}",
            'HTTP_Accept'        => 'application/json',
        ] + $append;
    }
}