<?php

namespace Appkr\Api\Example;

use DB;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sqlite = in_array(config('database.default'), ['sqlite', 'testing'], true);

        if (! $sqlite) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        if (is_50() or is_51()) {
            Eloquent::unguard();
        }

        $faker = Faker::create();

        // Seeding authors table
        Author::truncate();

        foreach (range(1, 10) as $index) {
            Author::create([
                'name' => $faker->userName,
                'email' => $faker->safeEmail,
            ]);
        }

        $this->command->line("<info>Seeded:</info> authors table");

        // Seeding resources table
        Book::truncate();

        $authorIds = (is_50())
            ? Author::pluck('id')
            : Author::pluck('id')->toArray();

        foreach (range(1, 100) as $index) {
            Book::create([
                'title' => $faker->sentence(),
                'author_id' => $faker->randomElement($authorIds),
                'published_at' => $faker->dateTimeThisCentury,
                'description' => $faker->randomElement([$faker->paragraph(), null]),
                'out_of_print' => $faker->randomElement([true, false]),
                'created_at' => $faker->dateTimeThisYear,
            ]);
        }

        if (is_50() or is_51()) {
            Eloquent::regard();
        }

        if (! $sqlite) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->command->line("<info>Seeded:</info> books table");
    }
}
