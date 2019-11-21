<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Player\Deck as PlayerDeck;
use App\Models\Match;
use App\Models\Player;
use App\Models\Player\Card as PlayerCard;
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
                $cards = $user->player->cards()
                    ->get(['code','quantity'])
                    ->map(function($card) {
                        return [
                            'card_code' => $card->code,
                            'quantity' => $card->quantity
                        ];
                    });
                return response()->json(['cards' => $cards, 'message' => 'CARDS FOUND'], 201);
            }
            return response()->json(['message' => 'Error retrieving cards.'], 409); 
        }
        return response()->json(['message' => 'Authorization error.'], 410);
    }

    public function addCard(Request $request) {

        $this->validate($request, [
            'card_code' => 'required'
        ]);

        $token = $request->header('authorization');
        if ($token) {
            $user = User::getByToken($token);

            if ($user && $user->player) {
                $card = Card::firstOrCreate([
                    'code' => $request->card_code
                ]);
                $playerCard = PlayerCard::firstOrCreate([
                    'card_uuid' => $card->uuid,
                    'player_uuid' => $user->player->uuid 
                ]);
                return response()->json(['message' => 'card added successfully.'], 201);
            }
        }
        return response()->json(['message' => 'Authorization error.'], 410);
    }

    public function getFavoriteDecks(Request $request) {
        $token = $request->header('authorization');
        if ($token) {
            $user = User::getByToken($token);
            if ($user && $user->player) {
                $decks = $user->player->decks()
                    ->whereNull('deleted_at')
                    ->get(['code','region1','region2']);
                return response()->json(['decks' => $decks, 'message' => 'DECKS FOUND'], 201);
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
                $deck = Deck::firstOrCreate([
                    'code' => $request->deck_code
                ]);
                $playerDeck = \DB::table('player_decks')
                    ->where('player_uuid', $user->player->uuid)
                    ->where('deck_uuid', $deck->uuid)
                    ->first();
                if (!$playerDeck) {
                    $playerDeck = PlayerDeck::create([
                        'player_uuid' => $user->player->uuid,
                        'deck_uuid' => $deck->uuid
                    ]);
                } else if ($playerDeck->deleted_at) {
                    \DB::table('player_decks')
                        ->where('uuid', $playerDeck->uuid)
                        ->update([
                            'deleted_at' => null
                        ]);
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
                $deck = Deck::where('code', $request->deck_code)->first();
                if (!$deck) {
                    return response()->json(['message' => 'Deck not found.'], 204);
                }
                $playerDeck = PlayerDeck::where('player_uuid', $user->player->uuid)
                    ->where('deck_uuid', $deck->uuid)
                    ->first();
                if (!$playerDeck) {
                    return response()->json(['message' => 'Deck not found.'], 204);
                } else if (!$deck->deleted_at) {
                    $playerDeck->delete();
                }
                return response()->json(['message' => 'DECK UNFAVORITED'], 201);
            }
            return response()->json(['message' => 'Error retrieving cards.'], 409); 
        }
        return response()->json(['message' => 'Authorization error.'], 410);

    }

}

