<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class MatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $users = \DB::table('users')->get()->pluck('uuid');
        $decks = [
            "CEBACAICHACACAIDCQTDIAQDAEBCSKZRBAAQCAISCMOR4KRQG4BACAIBE4BACAQBEE",
            "CEBAGAIDCYSC4BQBAIEAWDZEFM4AEAYBAIJBMMIDAEBQUMRWAEAQCAZR",
            "CEBAGAIDCQRSOCQBAQAQYDISDQTCOKBNGQAACAIBAMFQ",
            "CEBACAIADIBQCAQTEA3QEBABAIGBUMJZAQAQABYMEASQEBQBAABQMERKGM2AMAICAEBAUHRFFU",
            "CEAQYAIDBAGA6EARCMKBSGQ7EMTACAQBAMMCYAA",
            "CEAQUAIBA4HBEJJHFEWTAMJTAIAQCAAMAMAQCGQ7GQAQEAIBAU4A",
            "CEBAIAIACYMBUNQJAECQODASDYQCSKZQGEAACAIBAURQ",
            "CEAQEAIFEI3AEAQBAIGCSCQBAUAQ6IBIFEVCWLJQGEBAGAICAEYTSBYBAUBR2HRBEMTDI",
            "CEBAIAIACYMBUNQJAECQODASDYQCSKZQGEAACAIBAURQ",
            "CEBAEAICAYNAEAIED4SAEBABAIBAYKBRAYAQIBASCMNTIOACAQAQEAILEUUQIAIEAYHBAMI",
            "CEBAIAIFAEHSQNQIAEAQGDAUDAQSOKJUAIAQCBI5AEAQCFYA",
            "CEAQ2AIEAECAQCQZDYPSILRRGQ2TUAABAEAQIBI",
            "CEBAIAICAYEQ2GYHAECQOCQVC4PSEKQCAEAQKFQCAEBAGKQBAEAQEGA",
            "CEBAIAIFAEOSQNQJAECAIDQ3D4SDANBYHEAACAIBAQYQ",
            "CEBAIAIEBAFBSNAEAECQCEQUDQBAGAIEAQXDUBIBAUHSEIZOGYAA",
            "CEBACAICGEFACAIBBMKBQHRGFIZDINYCAEAQENYCAEAQGEYBAEAQEGA",
            "CEBAEAIFAEUAOAIEAQNSIMBUGU4AEAYBAUOTCNQDAECB6OJ2AEAQCBJC",
            "CEBAIAICCMQCKNYIAEAASDIPDEQCKLBNAIAQCAQWAEAQANAA",
            "CEBAGAIDEQXDMBQBAIEASDYYFM4AEAQBAILDCAYBAMFBMMQCAEAQGMICAEBAWEQ",
            "CEBAGAIACYOSMBYBAIBAMCIMDANDSAQCAEABKKYDAEBAWFRHAA",
            "CEBAIAIBBEGSGLQFAEBQKBQ6F4ZAEAIBAEWQGAIDCMQDIAQBAEARIBABAMLB2KZQ"
        ];
        foreach (range(1,200) as $i) {
            $player1 = $users->random(1)[0];
            do {
                $player2 = $users->random(1)[0];
            } while($player1 != $player2);
            
            $result = rand(0,1);
            
            $player1_deck = rand(0, count($decks) - 1);
            $player2_deck = rand(0, count($decks) - 1);

            \DB::table('player_matches')
                ->insert(
                    [
                        'uuid' => Str::uuid()->toString(),
                        'player_uuid' => $player1,
                        'deck_code' => $decks[$player1_deck],
                        'result' => $result
                    ],
                    [
                        'uuid' => Str::uuid()->toString(),
                        'player_uuid' => $player2,
                        'deck_code' => $decks[$player2_deck],
                        'result' => $result
                    ]
            );
        }
    }
}
