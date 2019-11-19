<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deck;
use App\Models\Match;
use App\Models\Player;
use App\Models\User;
use Carbon\Carbon;

use \Firebase\JWT\JWT;

class MyController extends Controller
{

    public function cards(Request $request) {

        $token = $request->header('authorization');
        if ($token) {
            $user = User::getByToken($token);

            if ($user && $user->player) {
                return response()->json(['cards' => $user->player->cards()->get(['card_code', 'quantity']), 'message' => 'CARDS FOUND'], 201);
            }
            return response()->json(['message' => 'Error retrieving cards.'], 409); 
        }
        return response()->json(['message' => 'Authorization error.'], 410);
    }

    public function getFavoriteDecks(Request $request) {
        $token = $request->header('authorization');
        if ($token) {
            $user = User::getByToken($token);
            if ($user && $user->player) {
                return response()->json(['decks' => $user->player->decks->pluck('deck_code'), 'message' => 'DECKS FOUND'], 201);
            }
        }
        return response()->json(['message' => 'Authorization error.'], 410);
    }

    public function addDeckToFavorites(Request $request) {

        $this->validate($request, [
            'deck_code' => 'required'
        ]);

        $token = $request->header('authorization');
        if ($token) {
            $user = User::getByToken($token);

            if ($user && $user->player) {

                $deck = \DB::table('player_decks')
                    ->where('player_uuid', $user->player->uuid)
                    ->where('deck_code', $request->deck_code)
                    ->first();
                if (!$deck) {
                    $deck = Deck::create([
                        'player_uuid' => $user->player->uuid,
                        'deck_code' => $request->deck_code
                    ]);
                } else if ($deck->deleted_at) {
                    $deck = Deck::find($deck->uuid);
                    $deck->update(['deleted_at' => null]);
                    $deck->refresh();
                }
                return response()->json(['deck' => $deck, 'message' => 'DECK FAVORITED'], 201);
            }
            return response()->json(['message' => 'Error retrieving cards.'], 409); 
        }
        return response()->json(['message' => 'Authorization error.'], 410);

    }

    public function removeDeckFromFavorites(Request $request) {

        $this->validate($request, [
            'deck_code' => 'required'
        ]);

        $token = $request->header('authorization');
        if ($token) {
            $user = User::getByToken($token);

            if ($user && $user->player) {

                $deck = \DB::table('player_decks')
                    ->where('player_uuid', $user->player->uuid)
                    ->where('deck_code', $request->deck_code)
                    ->first();
                if (!$deck) {
                    return response()->json(['message' => 'Deck not found.'], 204);
                } else if (!$deck->deleted_at) {
                    $deck = Deck::find($deck->uuid);
                    $deck->update(['deleted_at' => Carbon::now()]);
                    $deck->refresh();
                }
                return response()->json(['message' => 'DECK UNFAVORITED'], 201);
            }
            return response()->json(['message' => 'Error retrieving cards.'], 409); 
        }
        return response()->json(['message' => 'Authorization error.'], 410);

    }

}

