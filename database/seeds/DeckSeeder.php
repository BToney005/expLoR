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

        $ranks = [
            [1, 'S', '100'],
            [2, 'A','75'],
            [3, 'B', '25'],
            [4, 'C', '5']
        ];
        foreach ($ranks as $rank) {
            \DB::table('ranks')
                ->insert(
                    [
                        'rank_id' => $rank[0],
                        'rank' => $rank[1],
                        'lower_bound' => $rank[2]
                    ]
                    );
        }
    }
}
