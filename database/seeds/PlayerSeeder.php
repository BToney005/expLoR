<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;


class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        \DB::table('players')
            ->insert([
                'uuid' => Str::uuid()->toString(),
                'name' => 'BToney005',
            ]);

        foreach (range(1,20) as $i) {
            \DB::table('players')
                ->insert([
                    'uuid' => Str::uuid()->toString(),
                    'name' => $faker->username
                ]);
        } 
    }
}
