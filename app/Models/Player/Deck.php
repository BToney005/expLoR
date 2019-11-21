<?php
namespace App\Models\Player;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;

class Deck extends Model 
{
    use UsesUuid, SoftDeletes;

    protected $table = 'player_decks';

    protected $guarded = [];

    /**
     * Relationships
     */

    public function deck() {
        return $this->belongsTo('App\Models\Deck');
    }

    public function player() {
        return $this->belongsTo('App\Models\Player');
    }
}

