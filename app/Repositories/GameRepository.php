<?php

namespace App\Repositories;

use App\Models\Game;

/**
 *
 */
class GameRepository extends AbstractRepository
{

    /**
     * @return string
     */
    protected function classModel()
    {
        return Game::class;
    }
}