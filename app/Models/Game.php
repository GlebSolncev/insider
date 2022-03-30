<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'goals',
        'match_game_id',
        'club_id'
    ];

    public $timestamps = false;

    public function club(){
        return $this->belongsTo(Club::class);
    }
}