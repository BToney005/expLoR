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
}
