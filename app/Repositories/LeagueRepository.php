<?php

namespace App\Repositories;

use App\Models\League;

class LeagueRepository extends AbstractRepository
{

    protected function classModel()
    {
        return League::class;
    }
}