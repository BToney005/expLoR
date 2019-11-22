<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $decks = [
            "CEBAGAIDCYSC4BQBAIEAWDZEFM4AEAYBAIJBMMIDAEBQUMRWAEAQCAZR",
            "CEBAGAIDCQRSOCQBAQAQYDISDQTCOKBNGQAACAIBAMFQ",
            "CEAQ2AIEAECAQCQZDYPSILRRGQ2TUAABAEAQIBI",
            "CEBAEAIFAEUAOAIEAQNSIMBUGU4AEAYBAUOTCNQDAECB6OJ2AEAQCBJC",
            "CEBAIAICCMQCKNYIAEAASDIPDEQCKLBNAIAQCAQWAEAQANAA",
            "CEBAGAIDEQXDMBQBAIEASDYYFM4AEAQBAILDCAYBAMFBMMQCAEAQGMICAEBAWEQ",
            "CEBAIAIBBEGSGLQFAEBQKBQ6F4ZAEAIBAEWQGAIDCMQDIAQBAEARIBABAMLB2KZQ"
        ];
        foreach ($decks as $deck) {
            \DB::table('decks')
                ->insert(
                    [
                        'uuid' => Str::uuid()->toString(),
                        'code' => $deck,
                        'region1' => 'Noxus',
                    ]
                );
        }
    }
}
