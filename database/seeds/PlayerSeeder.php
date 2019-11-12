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
        \DB::table('users')
            ->insert([
                'uuid' => Str::uuid()->toString(),
                'username' => 'BToney005',
                'email' => 'btoney434@gmail.com',
                'password' => app('hash')->make('secret1234') 
            ]);
        foreach (range(1,20) as $i) {
            \DB::table('users')
                ->insert([
                    'uuid' => Str::uuid()->toString(),
                    'username' => $faker->username,
                    'email' => $faker->email,
                    'password' => app('hash')->make('secret1234') 
                ]);
        } 
    }
}
