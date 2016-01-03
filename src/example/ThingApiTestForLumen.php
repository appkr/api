<?php

namespace Appkr\Fractal\Example;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ThingApiTestForLumen extends \TestCase
{
    use DatabaseTransactions;

    /**
     * Stubbed Author model
     *
     * @var \Appkr\Fractal\Example\Author
     */
    protected $author;

    /**
     * Stubbed thing
     *
     * @var array
     */
    protected $things = [];

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

        $this->author = \Appkr\Fractal\Example\Author::create([
            'name'  => 'foo',
            'email' => $faker->safeEmail
        ]);

        $this->things = \Appkr\Fractal\Example\Thing::create([
            'title'       => $faker->sentence(),
            'author_id'   => $this->author->id,
            'description' => $faker->randomElement([$faker->paragraph(), null]),
            'deprecated'  => $faker->randomElement([0, 1])
        ])->toArray();
    }

    /** @test */
    public function it_fetches_a_collection_of_things()
    {
        $this->get('v1/things', $this->getHeaders())
            ->seeStatusCode(200)
            ->seeJson();
    }

    /** @test */
    public function it_fetches_a_instance_of_thing()
    {
        $this->get('v1/things/' . $this->things['id'], $this->getHeaders())
            ->seeStatusCode(200)
            ->seeJson();
    }

    /** @test */
    public function it_responds_404_if_requested_thing_is_not_found()
    {
        $this->get('v1/things/100000', $this->getHeaders())
            ->seeStatusCode(404)
            ->seeJson();
    }

    /** @test */
    public function it_responds_422_if_create_thing_request_fails_validation()
    {
        $payload = [
            'title'       => null,
            'author_id'   => null,
            'description' => 'n'
        ];

        $this->post('v1/things', $payload, $this->getHeaders())
            ->seeStatusCode(422)
            ->seeJson();
    }

    /** @test */
    public function it_responds_201_after_creation()
    {
        $payload = [
            'title'       => 'new title',
            'author_id'   => $this->author->id,
            'description' => 'new description'
        ];

        $this->actingAs($this->author)
            ->post('v1/things', $payload, $this->getHeaders())
            ->seeInDatabase('things', ['title' => 'new title'])
            ->seeStatusCode(201)
            ->seeJsonContains(['title' => 'new title']);
    }

    /** @test */
    public function it_responds_200_if_a_update_request_success()
    {
        $this->actingAs($this->author)
            ->put(
                'v1/things/' . $this->things['id'],
                ['title' => 'MODIFIED title', '_method' => 'PUT'],
                $this->getHeaders()
            )
            ->seeInDatabase('things', ['title' => 'MODIFIED title'])
            ->seeStatusCode(200)
            ->seeJson();
    }

    /** @test */
    public function it_responds_200_if_a_delete_request_success()
    {
        $this->actingAs($this->author)
            ->delete(
                'v1/things/' . $this->things['id'],
                ['_method' => 'DELETE'],
                $this->getHeaders()
            )
            ->notSeeInDatabase('things', ['id' => $this->things['id']])
            ->seeStatusCode(200)
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
            'HTTP_Accept'        => 'application/json'
        ] + $append;
    }
}