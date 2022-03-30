<?php

namespace App\Repositories;

use App\Models\PlayerSkill;

/**
 *
 */
class PlayerSkillRepository extends AbstractRepository
{

    /**
     * @return string
     */
    protected function classModel()
    {
        return PlayerSkill::class;
    }
}