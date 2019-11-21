<?php
namespace App\Models\Deck;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Keyword extends Model 
{
    use UsesUuid;

    protected $table = 'deck_keywords';
    public $timestamps = false;

    protected $guarded = [];


}




