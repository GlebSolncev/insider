<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;

/**
 *
 */
class MatchGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id'
    ];

    public function games(){
        return $this->hasMany(Game::class);
    }
}
