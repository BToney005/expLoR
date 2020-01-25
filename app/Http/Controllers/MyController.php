<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Player\Deck as PlayerDeck;
use App\Models\Match;
use App\Models\Player;
use App\Models\Rank;
use App\Models\Player\Card as PlayerCard;
use App\Models\User;
use Carbon\Carbon;

use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;

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

            // $ranks = RANK::select()->orderBy('lower_bound', 'desc')->get()->toArray();
            // foreach($decks as $deck) {
            //     $deck->rank = assignRank($deck->score, $ranks);
            // }
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

        $regions = [
            "Neutral",
            "Noxus",
            "Demacia",
            "Freljord",
            "ShadowIsles",
            "Ionia",
            "PiltoverZaun"
        ];

        $this->validate($request, [
            'player_id' => 'required',
            // 'required_keywords' => 'required',
            // 'required_cards' => 'required_if:player_cards,true',
            'player_cards' => 'required|boolean'
        ]);

        $regionParams = [];
        $keywordParams = [];

        if ($request->has('required_keywords') && count($request->required_keywords)) {
            foreach ($request->required_keywords as $keyword) {
                if (in_array($keyword,$regions)) {
                    $regionParams[] = $keyword;
                }
                else {
                    $keywordParams[] = $keyword;
                }
            }
        }

        $player = Player::where('name', $request->player_id)->first();
        if (!$player) {
            return response()->json(['message' => 'Player not found'], 500);
        }

        $playerCards = $request->player_cards 
            ? $player->cards->pluck('uuid')->toArray() 
            : [];

        $cardUuids = $request->has('required_cards') && count($request->required_cards)
            ?
            Card::when($request->has('required_cards') && $request->required_cards, function ($query) use ($request) {
                $query->whereIn('code', $request->required_cards);
            })
            /*
            when(!$request->player_cards, function ($query) use ($request) {
                if ($request->has('required_cards') && count($request->required_cards)) {
                    $query->whereIn('code', $request->required_cards);
                }
            })
            ->when($request->player_cards, function ($query) use ($request,$player) {
                if ($request->has('required_cards') && count($request->required_cards)) {
                    $query->whereIn('code', $request->required_cards)
                        ->whereIn('code', $player->cards->pluck('code')->toArray());
                } else {
                    $query->whereIn('code', $player->cards->pluck('code')->toArray());
                }
            })
            */
            ->pluck('uuid')
            ->toArray()
            : [];

        /*
        if (!(count($request->required_cards) || count($request->required_keywords))) {
            return response()->json(['message' => 'No valid parameters given'], 500);
        }
        */

        $decks = \DB::table('decks')
            // ->leftJoin('deck_cards', 'decks.uuid','=','deck_cards.deck_uuid')
            ->leftJoin('deck_keywords', 'decks.uuid','=','deck_keywords.deck_uuid')
            ->select([
                'decks.*',
            ])
            /*
            ->when(count($cardUuids), function ($query) use ($cardUuids) {
                $query->whereIn('deck_cards.card_uuid', $cardUuids);
            })
            */
            ->when(count($request->required_keywords), function ($query) use ($regionParams, $keywordParams) {
                $query->when(count($regionParams), function ($q) use ($regionParams) {
                    if (count($regionParams) == 1) {
                        $region = $regionParams[0];
                        /*
                        $q->where(function ($q1) use ($region) {
                            $q1->where('decks.region1', $region)
                                ->whereNull('decks.region2');
                        })->orWhere(function ($q2) use ($region) {
                            $q2->whereNull('decks.region1')
                                ->where('decks.region2',$region);
                        });
                        */
                        $q->where('decks.region1', $region)
                            ->orWhere('decks.region2', $region);
                    } 
                    else if (count($regionParams) == 2) {
                        $q->whereIn('decks.region1', $regionParams)
                            ->whereIn('decks.region2', $regionParams);
                    } else {
                        $q->whereRaw('1=0');
                    }
                });
                /*
                ->when(count($keywordParams), function ($q) use ($keywordParams) {
                    $q->whereIn('deck_keywords.keyword', $keywordParams);
                });
                */
            })
            ->groupBy('decks.uuid')
            ->get()
            ->filter(function ($deck) use ($request, $cardUuids, $keywordParams, $playerCards) {
                $deckCards = \DB::table('deck_cards')
                    ->where('deck_uuid', $deck->uuid)
                    ->pluck('card_uuid')
                    ->toArray();
                
                if (count($cardUuids)) {
                    foreach ($cardUuids as $cardUuid) {
                        if (!in_array($cardUuid, $deckCards)) {
                            return false;
                        } 
                    }
                }

                if (count($playerCards)) {
                    foreach ($deckCards as $deckCard) {
                        if (!in_array($deckCard, $playerCards))
                            return false;
                    }
                }

                $deckKeywords = \DB::table('deck_keywords')
                    ->where('deck_uuid', $deck->uuid)
                    ->pluck('keyword')
                    ->toArray();

                if (count($deckKeywords)) {
                    foreach ($keywordParams as $param) {
                        if (!in_array($param,$deckKeywords)) {
                            return false;
                        }
                    }
                }

                return true;
            })
            ->map(function ($deck) use ($player) {
                $deck->bookmarked = $player->decks()->where('deck_uuid', $deck->uuid)
                    ->whereNull('deleted_at')->count();
                return $deck;
            })
            ->sortByDesc(function ($deck, $key) {
                return Deck::find($deck->uuid)
                    ->score;
            })
            ->take(20)
            ->values();

        // assign rank to deck code
        $ranks = RANK::select()->orderBy('lower_bound', 'desc')->get()->toArray();
        foreach($decks as $deck) {
            $deck_matches = MATCH::select(DB::raw('deck_code, sum(result) * sum(result) / count(result) as score'))
            ->where('deck_code', '=', $deck->code)
            ->whereBetween('created_at', [Carbon::now()->subMonths(1), Carbon::now()])
            ->groupBy('deck_code')
            ->orderBy('score', 'desc')
            ->get()
            ->toArray();
            if (count($deck_matches)) {
                $score = $deck_matches[0]["score"];
                $deck->rank = assignRank($score, $ranks);
            }
        }
        return response()->json(['decks' => $decks, 'message' => 'DECKS FOUND'], 201);
    }

}

