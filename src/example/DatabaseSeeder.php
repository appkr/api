<?php

namespace Appkr\Fractal\Example;

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
        Eloquent::unguard();
        $faker = Faker::create();

        // Seeding authors table
        Author::truncate();

        foreach (range(1, 10) as $index) {
            Author::create([
                'name'  => $faker->userName,
                'email' => $faker->safeEmail
            ]);
        }

        $this->command->line("<info>Seeded:</info> authors table");

        // Seeding resources table
        Thing::truncate();

        $authorIds = (is_51())
            ? Author::lists('id')->toArray()
            : Author::lists('id');

        foreach (range(1, 100) as $index) {
            Thing::create([
                'title'       => $faker->sentence(),
                'author_id'   => $faker->randomElement($authorIds),
                'description' => $faker->randomElement([$faker->paragraph(), null]),
                'deprecated'  => $faker->randomElement([0, 1])
            ]);
        }

        $this->command->line("<info>Seeded:</info> things table");
    }
}
