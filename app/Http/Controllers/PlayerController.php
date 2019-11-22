<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Deck\Keyword as DeckKeyword;
use App\Models\Match;
use App\Models\Player;
use Carbon\Carbon;

use \Firebase\JWT\JWT;

class PlayerController extends Controller
{
    public function recordMatch(Request $request) {

        $this->validate($request, [
            'player_id' => 'required',
            'deck_code' => 'required',
            'result' => 'required|boolean',
            'regions' => 'required',
            'cards' => 'required',
            'keywords' => 'required'
        ]);

        $player = Player::firstOrCreate([
            'name' => $request->player_id
        ]);

        if ($player) {
            $deck = Deck::firstOrCreate([
                'code' => $request->deck_code
            ]);
            foreach ($request->regions as $index => $region) {
                $i = $index + 1;
                if (!$deck->{"region{$i}"}) {
                    $deck->{"region{$i}"} = $region["regionRef"];
                    $deck->save();
                }
            }
            foreach ($request->keywords as $keyword) {
                DeckKeyword::firstOrCreate([
                    'deck_uuid' => $deck->uuid,
                    'keyword' => $keyword
                ]);
            }
            $match = Match::create([
                'player_uuid' => $player->uuid, 
                'deck_code' => $request->deck_code,
                'result' => (bool) $request->result
            ]);

            foreach ($request->cards as $card_code) {
                $card = Card::firstOrCreate(['code' => $card_code]);
                $pc = \DB::table('player_cards')
                    ->where([
                        'player_uuid' => $player->uuid,
                        'card_uuid' => $card->uuid,
                    ])->first();
                if (!$pc) {
                    \DB::table('player_cards')->insert([
                        'uuid' => Str::uuid()->toString(),
                        'player_uuid' => $player->uuid,
                        'card_uuid' => $card->uuid
                    ]);
                }
                $dc = \DB::table('deck_cards')
                    ->where([
                        'deck_uuid' => $deck->uuid,
                        'card_uuid' => $card->uuid
                    ])->first();
                if (!$dc) {
                    \DB::table('deck_cards')->insert([
                        'uuid' => Str::uuid()->toString(),
                        'deck_uuid' => $deck->uuid,
                        'card_uuid' => $card->uuid
                    ]);
                }
            }

            return response()->json(['match' => $match->uuid, 'message' => 'MATCH SAVED'], 201);
        }
        return response()->json(['message' => 'Match Creation Failed!'], 409);
    }

    public function stats(Request $request) {
        $this->validate($request, [
            'player_name' => 'required'
        ]);

        $player = Player::where('name', $request->player_name)
            ->first();

        if ($player) {
            $matchHistory = $player->matches
                ->map(function($match) {
                    return [
                        'deck' => $match->deck_code,
                        'result' => $match->result,
                        'timestamp' => $match->created_at
                    ];
                })
                ->sortByDesc('timestamp')
                ->toArray();

            $byDeck = $player->matches->groupBy('deck_code')
                ->map(function ($match, $deck_code) use (&$cards) {
                    $total = $match->where('deck_code', $deck_code)->count();
                    $wins = $match->where('deck_code', $deck_code)
                        ->where('result', true)
                        ->count();
                    return [
                        'wins' => $wins,
                        'losses' => $total - $wins,
                        'uses' => $total,
                    ];
                });

            $cards = [];
            foreach ($byDeck as $deck_code => $deck) {
                $ret = exec("cd ". base_path("resources/assets/js") ."; node runeterra.js {$deck_code} 2>&1", $out, $err);
                $dcs = collect(json_decode($ret))->each(function ($card) use (&$cards, $deck, $player) {
                    if (!isset($cards[$card->code])) {
                        $cards[$card->code] = [
                            'wins' => 0,
                            'losses' => 0
                        ];
                    }
                    $cards[$card->code]['wins'] += $deck['wins'];
                    $cards[$card->code]['losses'] += $deck['losses'];
                });
            }

            $stats['wins'] = $byDeck->sum('wins');
            $stats['losses'] = $byDeck->sum('losses');
            $stats['matches'] = $stats['wins'] + $stats['losses'];
            $stats['match_history'] = $matchHistory;
            $stats['decks'] = $byDeck; 
            $stats['cards'] = $cards;

            return response()->json(['stats' => $stats, 'message' => 'STATS FOUND'], 200);
        }
        return response()->json(['message' => 'Error retrieving stats.'], 409); 
    }

}
