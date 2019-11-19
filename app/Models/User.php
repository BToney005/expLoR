<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use App\Traits\UsesUuid;

use \Firebase\JWT\JWT;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, UsesUuid, SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Relationships
     */

    public function player() {
        return $this->belongsTo(Player::class, 'player_uuid');
    }

    /**
     * Methods      
     */

    public static function getByToken($token) {
        $jwt = explode(' ', $token)[1];
        $key = env('JWT_SECRET');
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        return User::find($decoded->sub);
    }
}