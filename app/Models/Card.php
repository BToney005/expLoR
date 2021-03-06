<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Card extends Model 
{
    use UsesUuid;

    public $timestamps = false;

    protected $guarded = [];
}


