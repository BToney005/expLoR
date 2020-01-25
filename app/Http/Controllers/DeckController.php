<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Match;
use App\Models\Rank;
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

        $ranks = RANK::select()->orderBy('lower_bound', 'desc')->get()->toArray();
        foreach($top_decks as $deck) {
            $deck->rank = assignRank($deck->score, $ranks);
        }

        return response()->json(['top_decks' => $top_decks, 'message' => 'DECKS FOUND'], 200);
    }
}
