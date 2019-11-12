<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Match;
use App\Models\User;
use Uuid;

use \Firebase\JWT\JWT;

class PlayerController extends Controller
{
    public function recordMatch(Request $request) {

        $this->validate($request, [
            'player_name' => 'required',
            'deck_code' => 'required',
            'result' => 'required|boolean',
            'datetime' => 'required'
        ]);

        $user = User::where('username',$request->player_name)
            ->first();

        if ($user) {
            $match = Match::create([
                'player_uuid' => $user->uuid, 
                'deck_code' => $request->deck_code,
                'result' => (bool) $request->result
            ]);
            return response()->json(['match' => $match->uuid, 'message' => 'MATCH SAVED'], 201);
        }
        return response()->json(['message' => 'Match Creation Failed!'], 409);
    }

    public function stats(Request $request) {
        $this->validate($request, [
            'player_name' => 'required'
        ]);

        $user = User::where('username', $request->player_name)
            ->first();

        if ($user) {
            $byDeck = $user->matches->groupBy('deck_code')
                ->map(function ($match, $deck_code) {
                    $total = $match->where('deck_code', $deck_code)->count();
                    $wins = $match->where('deck_code', $deck_code)
                        ->where('result', true)
                        ->count();
                    return [
                        'wins' => $wins,
                        'losses' => $total - $wins,
                        'uses' => $total 
                    ];
                });

            $stats['wins'] = $byDeck->sum('wins');
            $stats['losses'] = $byDeck->sum('losses');
            $stats['matches'] = $stats['wins'] + $stats['losses'];
            $stats['decks'] = $byDeck; 

            return response()->json(['stats' => $stats, 'message' => 'STATS FOUND'], 201);
        }
        return response()->json(['message' => 'Error retrieving stats.'], 409); 
    }
}
