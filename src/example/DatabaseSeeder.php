<?php

namespace Appkr\Api\Example;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Model as Eloquent;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! is_52()) {
            Eloquent::unguard();
        }

        $faker = Faker::create();

        // Seeding authors table
        Author::truncate();

        foreach (range(1, 10) as $index) {
            Author::create([
                'name'  => $faker->userName,
                'email' => $faker->safeEmail,
            ]);
        }

        $this->command->line("<info>Seeded:</info> authors table");

        // Seeding resources table
        Book::truncate();

        $authorIds = (is_50())
            ? Author::lists('id')
            : Author::lists('id')->toArray();

        foreach (range(1, 100) as $index) {
            Book::create([
                'title'       => $faker->sentence(),
                'author_id'   => $faker->randomElement($authorIds),
                'published_at'=> $faker->dateTimeThisCentury,
                'description' => $faker->randomElement([$faker->paragraph(), null]),
                'out_of_print'=> $faker->randomElement([0, 1]),
                'created_at'  => $faker->dateTimeThisYear,
            ]);
        }

        if (! is_52()) {
            Eloquent::regard();
        }

        $this->command->line("<info>Seeded:</info> books table");
    }
}
