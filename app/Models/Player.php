<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 */
class Player extends Model
{
    use HasFactory;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $with = ['skills'];

    /**
     * @var string[]
     */
    protected $fillable = [
        'api_id',
        'last_name',
        'first_name',
        'position',
        'rating',
        'club_id',
    ];

    /**
     * @return HasMany
     */
    public function skills(){
        return $this->hasMany(PlayerSkill::class);
    }
}
