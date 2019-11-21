<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Deck extends Model 
{
    use UsesUuid;

    protected $guarded = [];

    /**
     * Relationships
     */

    public function cards() {
        return $this->belongsToMany(Card::class, 'deck_cards', 'card_uuid', 'deck_uuid');
    }

    public function keywords() {
        return $this->hasMany(DeckKeyword::class);
    }

    /**
     * Methods 
     */

    public function getRegionsAttribute() {
        if ($this->region2) {
            return [
                $this->region1,
                $this->region2
            ];
        }
        return [$this->region1];
    }
}
