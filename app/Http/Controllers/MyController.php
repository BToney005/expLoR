<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            $jwt = explode(' ', $token)[1];
            $key = env('JWT_SECRET');
            $decoded = JWT::decode($jwt, $key, array('HS256'));

            $user = User::find($decoded->sub);

            if ($user && $user->player) {
                return response()->json(['cards' => $user->player->cards()->get(['card_code', 'quantity']), 'message' => 'CARDS FOUND'], 201);
            }
            return response()->json(['message' => 'Error retrieving cards.'], 409); 
        }
        return response()->json(['message' => 'Authorization error.'], 410);
    }

}

