<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Match;
use Carbon\Carbon;

use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;


class RankController extends Controller
{
    public function setRanks() {
        $sorted_decks = MATCH::select(DB::raw('deck_code, sum(result) * sum(result) / count(result) as score'))
            ->whereBetween('created_at', [Carbon::now()->subMonths(1), Carbon::now()])
            ->groupBy('deck_code')
            ->orderBy('score')
            ->get()
            ->toArray();
        $quintile_groups = array_chunk($sorted_decks, count($sorted_decks) / 5);
        $quintile_boundaries = array_map(function($ntile) {
                return $ntile[0]['score'];
            }, $quintile_groups);

        $quintiles = array(
            array('rank'=>'S', 'lower_bound'=> $quintile_boundaries[4]),
            array('rank'=>'A', 'lower_bound'=> $quintile_boundaries[3]),
            array('rank'=>'B', 'lower_bound'=> $quintile_boundaries[2]),
            array('rank'=>'C', 'lower_bound'=> $quintile_boundaries[1]),
        );
        // DB::table('ranks')->delete();
        \DB::table('ranks')->insert($quintiles);
        return response()->json(['message' => 'QUINTILES SET'], 200);
    }
}
