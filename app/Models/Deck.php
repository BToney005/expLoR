<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Deck extends Model 
{
    use UsesUuid;

    protected $table = 'player_decks';

    protected $guarded = [];

    public function player() {
        return $this->belongsTo(Player::class);
    }
}
