<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Match;
use App\Models\Player;
use Carbon\Carbon;

use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;


class DeckController extends Controller
{
    public function topDecks(Request $request) {
        $this->validate($request, [
            'n' => 'required'
        ]);

        $top_decks = MATCH::select(DB::raw('deck_code, sum(result) * sum(result) / count(result) as score'))
            ->whereBetween('created_at', [Carbon::now()->subMonths(1), Carbon::now()])
            ->groupBy('deck_code')
            ->orderBy('score', 'desc')
            ->take($request->n)
            ->get();

        return response()->json(['top_decks' => $top_decks, 'message' => 'DECKS FOUND'], 200);
    }

    public function setRanks() {
        $sorted_decks = MATCH::select(DB::raw('deck_code, sum(result) * sum(result) / count(result) as score'))
            ->whereBetween('created_at', [Carbon::now()->subMonths(1), Carbon::now()])
            ->groupBy('deck_code')
            ->get();
        $quintile_boundaries = array_chunk($sorted_decks, 5)
            ->map(function($ntile) {
                return end($ntile)
            })
        $quintiles = array(
            array('rank'=>'S', 'lower_bound'=> $quintile_boundaries[0]),
            array('rank'=>'A', 'lower_bound'=> $quintile_boundaries[1]),
            array('rank'=>'B', 'lower_bound'=> $quintile_boundaries[2]),
            array('rank'=>'C', 'lower_bound'=> $quintile_boundaries[3]),
        );
        DB::table('ranks')->delete();
        DB::table('ranks')->insert($data);
        return response()->json(['message' => 'QUINTILES SET'], 200);
    }
}
