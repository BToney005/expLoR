<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Match extends Model 
{
    protected $table = 'player_matches';

    use UsesUuid;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'player_uuid', 'deck_code', 'result'
    ];
}
