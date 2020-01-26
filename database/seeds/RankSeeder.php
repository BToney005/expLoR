<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;


class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        \DB::table('ranks')
            ->insert([
                'rank_id' => 1,
                'rank' => 'S',
                'lower_bound' => 0
            ]);
    }
}