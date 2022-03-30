<?php

namespace App\Services;

use App\Repositories\PlayerSkillRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 *
 */
class PlayerSkillService extends AbstractService
{
    /**
     * @var PlayerSkillRepository
     */
    protected $repository;

    /**
     * @param PlayerSkillRepository $repository
     */
    public function __construct(PlayerSkillRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getFields(array $data): array
    {
        return [
            'group' => Arr::get($data, 'group'),
            'name'  => Arr::get($data, 'name'),
            'value' => Arr::get($data, 'value'),
        ];
    }

    /**
     * @param array $playerData
     * @return array
     */
    public function getImportData(array $playerData): array
    {
        foreach (Config::get('skills') as $group => $skillKeys) {
            foreach (Arr::only($playerData, $skillKeys) as $name => $value) {
                $insertData[] = [
                    'group' => $group,
                    'name'  => $name,
                    'value' => $value,
                ];
            }
        }
        return $insertData;
    }
}