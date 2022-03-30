<?php

namespace App\Services;

use App\Repositories\ClubRepository;
use ErrorException;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 *
 */
class ClubService extends AbstractService
{
    /**
     * @var ClubRepository $repository
     */
    protected ClubRepository $repository;

    /**
     * @param ClubRepository $repository
     */
    public function __construct(ClubRepository $repository)
    {
        $this->repository = $repository;

    }

    /**
     * @return Collection
     */
    public function getAllForResponse(): Collection
    {
        $collection = $this->repository->getWithWhere([], []);
        return $this->getClubsForResponse($collection);
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function getClubsForResponse(Collection $collection): Collection
    {
        return $collection->map(function ($model) {
            return $this->getForReponse($model);
        })->filter()->values();
    }

    /**
     * @param Model $model
     * @return Model|null
     */
    public function getForReponse(Model $model): Model|null
    {
        if ($model->players->count() < 11) {
            return null;
        }

        return $model->setVisible([
            'id',
            'name',
        ]);
    }

    /**
     * @param array $ids
     * @return Collection
     * @throws ErrorException
     */
    public function getByIds(array $ids): Collection
    {
        $models = $this->repository->whereIn('id', array_merge($ids, [1000]));

        $diff = array_diff(array_merge($ids, [1000]), $models->pluck('id')->toArray());
        if ($diff) {
            throw new ErrorException('Club not found. ' . json_encode(array_values($diff)));
        }

        return $models;
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function getById(int $id): Model|null
    {
        return $this->repository->getSingleWithWhere([], [['id', '=', $id]]);
    }

    /**
     * @param $id
     * @return Model
     * @throws ErrorException
     * @throws BindingResolutionException
     */
    public function getInfoByClubId($id): Model
    {
        $model = $this->getById($id);
        if (!$model) {
            throw new ErrorException('Club not found');
        }

        /** @var PlayerService $playerService */
        $playerService = Container::getInstance()->make(PlayerService::class);
        $model->power = $playerService->getSquardInfo($model->players->random(11));

        return $model;
    }

    // IMPORT

    /**
     * @param array $data
     * @return array
     */
    public function getFields(array $data): array
    {
        return [
            'api_id' => Arr::get($data, 'id'),
            'name'   => Arr::get($data, 'name'),
        ];
    }

    /**
     * @param array $data
     * @return Model
     */
    public function import(array $data): Model
    {
        $fields = $this->getFields($data);
        $model = $this->repository->getSingleWithWhere([], [['api_id', '=', $fields['api_id']]]);
        if (!$model) {
            $model = $this->repository->insertModel($fields);
        }
        return $model;
    }

    /**
     * @return void
     */
    public function clearDB(){
        $this->repository->clearAll();
    }
}