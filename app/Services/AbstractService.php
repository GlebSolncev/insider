<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
abstract class AbstractService
{
    /**
     * @param array $data
     * @param       $field
     * @param       $value
     * @return Collection|Model
     */
    public function createOrUpdate(array $data, $field, $value)
    {
        $model = $this->repository->getSingleWithWhere([], [[$field, '=', $value]]);

        if (!$model) {
            $model = $this->repository->insertModel($data);
        } else {
            $this->repository->updateModel($model, $data);
        }

        return $model;
    }
}