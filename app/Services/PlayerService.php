<?php

namespace App\Services;

use App\Models\Player;
use App\Repositories\PlayerRepository;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 *
 */
class PlayerService extends AbstractService
{
    /**
     * @var PlayerRepository $repository
     */
    protected PlayerRepository $repository;

    /**
     * @param PlayerRepository $repository
     */
    public function __construct(PlayerRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $players
     * @return array
     */
    public function getSquardInfo($players): array
    {
        $skills = Config::get('skills');
        $info = Config::get('power-skills');
        $result = [];

        $collection = $players->map(function ($player) use ($skills) {
            foreach ($skills as $name => $group) {
                $value = $player->skills->whereIn('name', $group)->sum('value') / 100;
                $player->$name = $value;
            }
            return $player;
        })->map(function ($player) use ($info) {
            foreach ($info as $name => $skills) {
                $value = array_sum($player->only($skills)) / $player->skills->count();
                $player->$name = round($value, 1);
            }

            return $player->only(array_keys($info));
        });

        foreach (array_keys($info) as $key) {
            $result[$key] = $collection->sum($key);
        }

        return $result;
    }

    /**
     * @param array $clubData
     * @return int
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function importClub(array $clubData): int
    {
        /** @var ClubService $clubService */
        $clubService = Container::getInstance()->make(ClubService::class);
        $model = $clubService->import($clubData);
        return $model->id;
    }

    /**
     * @param array $playerData
     * @return array
     */
    protected function getFields(array $playerData): array
    {
        return [
            'api_id'     => Arr::get($playerData, 'baseId'),
            'first_name' => Arr::get($playerData, 'firstName'),
            'last_name'  => Arr::get($playerData, 'lastName'),
            'position'   => Arr::get($playerData, 'position'),
            'rating'     => Arr::get($playerData, 'rating'),
            'club_id'    => Arr::get($playerData, 'club_id'),
        ];
    }

    /**
     * @param $players
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function import($players): void
    {
        /** @var PlayerSkillService $playerSkillService */
        $playerSkillService = Container::getInstance()->make(PlayerSkillService::class);

        $players->map(function ($playerData) use($playerSkillService) {
            if (Arr::get($playerData, 'club')) {
                $clubId = $this->importClub(Arr::get($playerData, 'club'));
                $playerData['club_id'] = $clubId;
                $playerFields = $this->getFields($playerData);
                /** @var Player $model */
                $model = $this->createOrUpdate($playerFields, 'api_id', $playerFields['api_id']);
                $model->skills()->delete();

                $insertData = $playerSkillService->getImportData($playerData);
                if ($insertData) {
                    $model->skills()->createMany($insertData);
                }
            }
        });
    }
}