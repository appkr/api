<?php

use Appkr\Api\Example\Author;
use Appkr\Api\Example\Book;
use Faker\Generator as Faker;

$factory->define(Author::class, function (Faker $faker) {
    return [
        'name' => $faker->userName,
        'email' => $faker->safeEmail,
    ];
});

$factory->define(Book::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'author_id' => $faker->randomElement([1, 2, 3]),
        'published_at' => $faker->dateTimeThisCentury,
        'description' => $faker->paragraph,
        'out_of_print' => $faker->randomElement([0, 1]),
        'created_at' => $faker->dateTimeThisYear,
    ];
});