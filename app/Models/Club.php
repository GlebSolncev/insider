<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Config;

/**
 *
 */
class Club extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'api_id',
        'name',
    ];

    /**
     * @var string[]
     */
    protected $with = ['players'];

    /**
     * @return mixed
     */
    protected function getSquardAttribute()
    {
        if($this->players->count() < 11) {
            return [];
        }
        $players = $this->players->random(11);
        $skills = Config::get('skills');
        $info = Config::get('power-skills');
        
        return $players->map(function ($player) use($skills){
            foreach($skills as $name => $group){
                $value = $player->skills->whereIn('name', $group)->sum('value');
                $player->$name = $value;
            }
            return $player;
        })->map(function($player) use($info){
            foreach ($info as $name => $skills){
                $player->$name = array_sum($player->only($skills));
            }

            return $player;
        });
    }

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return HasMany
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * @return BelongsToMany
     */
    public function clubs(){
        return $this->belongsToMany(MatchGame::class, 'match_games');
    }
}
