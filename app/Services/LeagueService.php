<?php

namespace App\Services;

use App\Repositories\LeagueRepository;
use ErrorException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 *
 */
class LeagueService extends AbstractService
{
    /**
     * @var LeagueRepository
     */
    protected $repository;
    /**
     * @var ClubService
     */
    protected $clubService;
    /**
     * @var GameService
     */
    protected $gameService;

    /**
     * @param LeagueRepository $repository
     * @param ClubService      $clubService
     * @param GameService      $gameService
     */
    public function __construct(LeagueRepository $repository, ClubService $clubService, GameService $gameService)
    {
        $this->repository = $repository;
        $this->clubService = $clubService;
        $this->gameService = $gameService;
    }

    /**
     * @param array $groups
     * @return array
     * @throws ErrorException
     */
    public function createLeague(array $groups): array
    {
        if (!$this->checkByModule(Collection::make($groups)->flatten(1)->count())) {
            throw new ErrorException('The number of teams does not fit. You need to choose 4,8,16 etc');
        }

        $clubs = $this->clubService->getByIds(Arr::flatten($groups, 1))->pluck('name', 'id');
        $league = $this->create();

        do {
            $winner = Collection::make([]);

            foreach ($groups as $group) {
                $matchInfoOne = $this->addMatch($league, $group);
                $matchInfoTwo = $this->addMatch($league, array_reverse($group));

                $mergeTwoMatches = Collection::make()
                    ->add($matchInfoOne)
                    ->add($matchInfoTwo)
                    ->flatten(1)
                    ->groupBy('club_id')
                    ->map(function ($group) {
                        $item = $group->first();
                        $item->goals = $group->sum('goals');
                        return $item;
                    });

                $winner->add(
                    $mergeTwoMatches->where('goals', $mergeTwoMatches->max('goals'))->first()
                );
            }

            $groups = $winner->pluck('club_id')->chunk(2)->toArray();
        } while (count(reset($groups)) > 1);

        return [
            'league_id' => $league->id,
            'clubs'     => $clubs,
            'max_weeks' => $league->matchGames->count(),
        ];
    }

    /**
     * @param int $leagueId
     * @return Model
     */
    public function getGamesById(int $leagueId): Model
    {
        $model = $this->repository->getSingleWithWhere([
            'matchGames',
            'matchGames.games',
            'matchGames.games.club',
        ], [['id', '=', $leagueId]]);

        $matchGames = $model->matchGames->map(function ($match) {
            $games = $this->gameService->getGamesForReponse($match->games);
            $match->setVisible([
                'id',
                'games',
            ]);
            $match->games = $games;
            return $match;
        })->sortBy('id')->values()->toArray();

        $model->setVisible([
            'id',
            'match_games',
        ]);
        $model->match_games = $matchGames;

        return $model;
    }

    /**
     * @param int $leagueId
     * @param int $week
     * @return Model
     * @throws ErrorException
     */
    public function getInfoByWeek(int $leagueId, int $week): Model
    {
        $model = $this->repository->getSingleWithWhere([], [['id', '=', $leagueId]]);
        if ($match = optional($model->matchGames)[$week - 1]) {
            $games = $this->gameService->getGamesForReponse($match->games);
            $match->games = $games;

            $model->setVisible([
                'id',
                'match_games',
            ]);
            $model->match_games = [$match];
        } else {
            throw new ErrorException('Match not have week: ' . ($week - 1));
        }

        return $model;
    }

    /**
     * @return Model
     */
    protected function create()
    {
        return $this->repository->insertModel([]);
    }

    /**
     * @param int $count
     * @return bool
     */
    protected function checkByModule(int $count): bool
    {
        $status = false;

        do {
            if ($count % 2 !== 0) {
                break;
            }

            $newCount = $count / 2;
            if ($newCount == 1) {
                $count = 1;
                $status = true;
            } else {
                $count = $count / 2;
            }
        } while ($count != 1);

        return $status;
    }

    /**
     * @param Model $league
     * @param array $group
     * @return Collection
     */
    protected function addMatch(Model $league, array $group): Collection
    {
        $games = Collection::make([]);
        $infoMatches = Collection::make()->add([
            $this->addGoals(reset($group), end($group)),
            $this->addGoals(end($group), reset($group)),
        ]);

        foreach ($infoMatches as $match) {
            $matchModel = $league->matchGames()->create();
            foreach ($match as $club) {
                $games->add($matchModel->games()->create([
                    'club_id' => Arr::get($club, 'id'),
                    'goals'   => Arr::get($club, 'goals'),
                ]));
            }
        }

        return $games;
    }

    /**
     * @param $fId
     * @param $lId
     * @return Model
     * @throws ErrorException
     * @throws BindingResolutionException
     */
    protected function addGoals($fId, $lId): Model
    {
        $gClub = $this->clubService->getInfoByClubId($fId);
        $sClub = $this->clubService->getInfoByClubId($lId);

        $onePower = Arr::get($gClub, 'power.strength') + Arr::get($gClub, 'power.attack');
        $twoPower = Arr::get($sClub, 'power.strength') + Arr::get($sClub, 'power.protection');

        $steps = ($onePower - $twoPower);
        if ($steps < 0) {
            $steps = $steps * (-1);
        }

        $goals = 0;
        for ($i = 0; $i < $steps; $i++) {
            $goals += rand(0, 1);
        }

        $gClub->goals = $goals;
        return $gClub;
    }

}