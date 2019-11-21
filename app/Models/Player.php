<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Player extends Model 
{

    use UsesUuid, SoftDeletes;

    protected $guarded = [];

    /**
     * Relationships
     */

    public function decks() {
        return $this->belongsToMany(Deck::class, 'player_decks');
    }

    public function matches() {
        return $this->hasMany(Match::class, 'player_uuid');
    }

    public function cards() {
        return $this->belongsToMany(Card::class, 'player_cards'); 
    }

}

