<?php

use Faker\Generator as Faker;

$factory->define(Appkr\Fractal\Example\Author::class, function (Faker $faker) {
    return [
        'name'  => $faker->userName,
        'email' => $faker->safeEmail
    ];
});

$factory->define(Appkr\Fractal\Example\Thing::class, function (Faker $faker) {
    return [
        'title'       => $faker->sentence,
        'author_id'   => $faker->randomElement([1, 2, 3]),
        'description' => $faker->paragraph,
        'deprecated'  => $faker->randomElement([0, 1])
    ];
});