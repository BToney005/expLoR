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
        $this->validate($request, [
            'player_name' => 'required'
        ]);

        $player = Player::where('name', $request->player_name)
            ->first();

        if ($player) {
            $cards = $player->cards()
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

    public function addCard(Request $request) {

        $this->validate($request, [
            'card_code' => 'required',
            'count' => 'required',
            'player_name' => 'required'
        ]);

        $player = Player::where('name', $request->player_name)
            ->first();

        $card = Card::firstOrCreate([
            'code' => $request->card_code
        ]);

        $existingCard = \DB::table('player_cards')
            ->where('player_uuid', $player->uuid)
            ->where('card_uuid', $card->uuid)
            ->first();
        if ($existingCard) {
            \DB::table('player_cards')
                ->where('uuid', $existingCard->uuid)
                ->update([
                    'quantity' => $request->count
                ]);
        } else {
            PlayerCard::create([
                'card_uuid' => $card->uuid,
                'player_uuid' => $player->uuid,
                'quantity' => $request->count
            ]);
        }

        return response()->json(['message' => 'card added successfully.'], 201);
    }

    public function getFavoriteDecks(Request $request) {
        $this->validate($request, [
            'player_name' => 'required'
        ]);

        $player = Player::where('name', $request->player_name)
            ->first();

        if ($player) {
            $decks = \DB::table('player_decks')
                ->where('player_uuid', $player->uuid)
                ->join('decks', 'decks.uuid', '=', 'player_decks.deck_uuid')
                ->whereNull('player_decks.deleted_at')
                ->get();
            return response()->json(['decks' => $decks, 'message' => 'DECKS FOUND'], 201);
        }
        return response()->json(['message' => 'Player not found.'], 410);
    }

    public function addDeckToFavorites(Request $request) {

        $this->validate($request, [
            'deck_code' => 'required',
            'player_name' => 'required'
        ]);

        $player = Player::where('name', $request->player_name)
            ->first();

        if ($player) {
                $deck = Deck::firstOrCreate([
                    'code' => $request->deck_code
                ]);
                $playerDeck = \DB::table('player_decks')
                    ->where('player_uuid', $player->uuid)
                    ->where('deck_uuid', $deck->uuid)
                    ->first();
                if (!$playerDeck) {
                    $playerDeck = PlayerDeck::create([
                        'player_uuid' => $player->uuid,
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

    public function removeDeckFromFavorites(Request $request) {

        $this->validate($request, [
            'deck_code' => 'required',
            'player_name' => 'required'
        ]);

        $player = Player::where('name', $request->player_name)
            ->first();

        if ($player) {
            $deck = Deck::where('code', $request->deck_code)->first();
            if (!$deck) {
                return response()->json(['message' => 'Deck not found.'], 204);
            }
            $playerDeck = PlayerDeck::where('player_uuid', $player->uuid)
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

    public function filterDecks(Request $request) {

        $this->validate($request, [
            'player_id' => 'required',
            // 'required_keywords' => 'required',
            'required_cards' => 'required_if:player_cards,true',
            'player_cards' => 'required|boolean'
        ]);

        $player = Player::where('name', $request->player_id)->first();
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 500);
        }
        $cardUuids = Card::when(!$request->player_cards, function ($query) use ($request) {
                $query->whereIn('code', $request->required_cards);
            })
            ->when($request->player_cards, function ($query) use ($request,$player) {
                $query->whereIn('code', $request->required_cards)
                    ->whereIn('code', $player->cards->pluck('code')->toArray());
            })
            ->pluck('uuid')
            ->toArray();
        if (!count($cardUuids) && !count($request->required_keywords)) {
            return response()->json(['message' => 'No valid parameters given'], 500);
        }

        $decks = \DB::table('decks')
            ->leftJoin('deck_cards', 'decks.uuid','=','deck_cards.deck_uuid')
            ->leftJoin('deck_keywords', 'decks.uuid','=','deck_keywords.deck_uuid')
            ->select([
                'decks.*',
            ])
            ->when(count($cardUuids), function ($query) use ($cardUuids) {
                $query->whereIn('deck_cards.card_uuid', $cardUuids);
            })
            ->when(count($request->required_keywords), function ($query) use ($request) {
                $query->whereIn('deck_keywords.keyword', $request->required_keywords);
            })
            ->groupBy('decks.uuid')
            ->get()
            ->sortByDesc(function ($deck, $key) {
                return Deck::find($deck->uuid)
                    ->score;
            })
            ->take(20);

        return response()->json(['decks' => $decks, 'message' => 'DECKS FOUND'], 201);
    }

}

