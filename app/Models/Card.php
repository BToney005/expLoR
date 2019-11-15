<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Card extends Model 
{
    use UsesUuid;

    protected $table = 'player_cards';
    public $timestamps = false;

    protected $guarded = [];

}


