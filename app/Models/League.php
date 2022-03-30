<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;

/**
 *
 */
class League extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function matchGames(){
        return $this->hasMany(MatchGame::class);
    }

    public function clubs(){
        return $this->belongsToMany(Club::class, 'match_games');
    }
}
