<?php

namespace App\Services;

use App\Repositories\GameRepository;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 *
 */
class GameService extends AbstractService
{
    /**
     * @var GameRepository
     */
    protected $repository;

    /**
     * @var ClubService
     */
    protected $clubService;

    /**
     * @param GameRepository $repository
     * @param ClubService    $clubService
     */
    public function __construct(GameRepository $repository, ClubService $clubService)
    {
        $this->repository = $repository;
        $this->clubService = $clubService;
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function getGamesForReponse(Collection $collection): Collection
    {
        return $collection->map(function ($model) {
            return $this->getForReponse($model);
        });
    }

    /**
     * @param Model $model
     * @return Model
     */
    public function getForReponse(Model $model): Model
    {
        $club = $this->clubService->getForReponse($model->club);

        $model->setVisible([
            'club',
            'goals',
            'match_game_id',
        ]);
        $model->club = $club;

        return $model;
    }
}