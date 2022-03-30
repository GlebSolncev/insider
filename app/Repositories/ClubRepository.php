<?php

namespace App\Repositories;

use App\Models\Club;

/**
 *
 */
class ClubRepository extends AbstractRepository
{
    /**
     * @return string
     */
    protected function classModel()
    {
        return Club::class;
    }
}