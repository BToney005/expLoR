<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Player;
use App\Models\User;

use \Firebase\JWT\JWT;



class AuthController extends Controller
{
    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {

        $this->validate($request, [
            // 'username' => 'required|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed'
        ]);

        try {

            $player = Player::firstOrCreate([
                'name' => $request->player_name
            ]);

            $user = User::create([
                'player_uuid' => $player->uuid,
                'email' => $request->email,
                'password' => app('hash')->make($request->password),
                'created_at' =>  date("Y-m-d H:i:s")
            ]);

            // $user->password = app('hash')->make($plainPassword);
        
            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }

    /**
     * Authenticate a user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'player_name' => 'required',
            'password' => 'required'
        ]);

        try { 
            $player = Player::where('name', $request->player_name)->first();
            if ($player) {
                $user = User::where('player_uuid', $player->uuid)->first();
                if($user && Hash::check($request->password, $user->password)){

                    $key = env('JWT_SECRET');
                    $token = [
                        "iss" => env("APP_URL"),
                        "aud" => env("APP_URL"),
                        "sub" => $user->uuid,
                        "iat" => time(),
                    ];
                    $jwt = JWT::encode($token, $key);

                    $user->auth_token = $jwt;
                    $user->save();

                    return response()->json(['token' => $user->auth_token, 'message' => 'FOUND'], 201);
                }
            }
        
            return response()->json(['user' => null, 'message' => 'NOT FOUND'], 202);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Login Failed!'], 409);
        }

    }
}