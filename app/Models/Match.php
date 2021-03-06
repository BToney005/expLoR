<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\UsesUuid;


class Match extends Model 
{
    protected $table = 'player_matches';

    use UsesUuid, SoftDeletes;

    protected $guarded = [];

}
