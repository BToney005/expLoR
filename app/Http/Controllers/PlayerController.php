<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Match;
use App\Models\Player;
use Carbon\Carbon;

use \Firebase\JWT\JWT;

class PlayerController extends Controller
{
    public function recordMatch(Request $request) {

        $this->validate($request, [
            'player_name' => 'required',
            'deck_code' => 'required',
            'result' => 'required|boolean'
            //'datetime' => 'required'
        ]);

        $player = Player::firstOrCreate([
            'name' => $request->player_name
        ])->first();

        if ($player) {
            $match = Match::create([
                'player_uuid' => $player->uuid, 
                'deck_code' => $request->deck_code,
                'result' => (bool) $request->result
                //'completed_at' => Carbon::createFromTimeStamp($request->datetime)->toDateTimeString()
            ]);
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
                $ret = exec("cd ". base_path("scripts") ."; node runeterra.js {$deck_code} 2>&1", $out, $err);
                $dcs = collect(json_decode($ret))->each(function ($card) use (&$cards, $deck) {
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
            $stats['decks'] = $byDeck; 
            $stats['cards'] = $cards;

            return response()->json(['stats' => $stats, 'message' => 'STATS FOUND'], 201);
        }
        return response()->json(['message' => 'Error retrieving stats.'], 409); 
    }

}
