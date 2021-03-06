<?php

namespace App\Observers;

use Illuminate\Support\Str;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Player;

class DeckObserver {
    public function created(Deck $deck) {
        $ret = exec("cd ". base_path("resources/assets/js") ."; node runeterra.js {$deck->code} 2>&1", $out, $err);
        $dcs = collect(json_decode($ret))->each(function ($card) use ($deck) {
            $cardModel = Card::firstOrCreate([
                'code' => $card->code
            ]);

            $deckCard = \DB::table('deck_cards')
                ->where('deck_uuid', $deck->uuid)
                ->where('card_uuid', $cardModel->uuid)
                ->first();

            if (!$deckCard) {
                \DB::table('deck_cards')
                    ->insert([
                        'uuid' => Str::uuid()->toString(),
                        'deck_uuid' => $deck->uuid,
                        'card_uuid' => $cardModel->uuid,
                        'count' => $card->count
                    ]);
            }
        });
    }
}