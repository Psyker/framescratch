<?php

use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        // Categories Seeding
        $data = [];
        $faker = Factory::create('en_EN');
        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'name' => $faker->word,
                'slug' => $faker->slug,
            ];
        }

        $this->table('categories')
            ->insert($data)
            ->save();

        // Posts Seeding
        $data = [];
        for ($i = 0; $i < 100; $i++) {
            $date = $faker->unixTime('now');
            $data[] = [
                'name' => $faker->catchPhrase,
                'slug' => $faker->slug,
                'category_id' => rand(1, 5),
                'content' => $faker->text(3000),
                'created_at' => date('Y-m-d H:i:s', $date),
                'updated_at' => date('Y-m-d H:i:s', $date),
            ];
        }

        $this->table('posts')
            ->insert($data)
            ->save();
    }
}
