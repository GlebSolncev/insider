<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeagueRequest;
use App\Services\LeagueService;
use ErrorException;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class LeagueController extends Controller
{
    /**
     * @var LeagueService
     */
    protected $service;

    /**
     * @param LeagueService $service
     */
    public function __construct(LeagueService $service)
    {
        $this->service = $service;
    }

    /**
     * @param LeagueRequest $request
     * @return array
     * @throws ErrorException
     */
    public function store(LeagueRequest $request)
    {
        return $this->service->createLeague($request->groups);
    }

    /**
     * @param LeagueRequest $request
     * @return Model
     */
    public function playAll(LeagueRequest $request)
    {
        return $this->service->getGamesById($request->league_id);
    }

    /**
     * @param LeagueRequest $request
     * @return Model
     * @throws ErrorException
     */
    public function nextWeek(LeagueRequest $request)
    {
        return $this->service->getInfoByWeek($request->league_id, $request->week);
    }
}