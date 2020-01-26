<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('RankSeeder');
        $this->call('PlayerSeeder');
        $this->call('MatchSeeder');
        $this->call('DeckSeeder');
    }
}
