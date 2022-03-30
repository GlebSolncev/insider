<?php

namespace App\Repositories;

use App\Models\Player;

/**
 *
 */
class PlayerRepository extends AbstractRepository
{

    /**
     * @return string
     */
    protected function classModel()
    {
        return Player::class;
    }
}