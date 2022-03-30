<?php

namespace App\Foundation\FootballManager\FifaManager\Services;

use App\Foundation\FootballManager\FifaManager\Requests\FifaRequests;
use App\Foundation\FootballManager\FifaManager\Responses\FifaResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FifaService
{
    /**
     *
     */
    const LEAGUE_ID = 13; // Primier League from FIFA

    /**
     * @var FifaRequests $request
     */
    protected $request;

    protected $response;

    /**
     * @param FifaRequests $request
     */
    public function __construct(FifaRequests $request)
    {
        $this->request = $request;
    }

    /**
     * @return $this
     */
    public function request()
    {
        $this->response = $this->request->request();

        return $this;
    }

    /**
     * @return FifaResponse
     */
    public function response()
    {
        $this->response = new FifaResponse($this->response);

        return $this->response;
    }

    /**
     * @param bool $test
     * @return Collection
     */
    public function getCollection(bool $test = false): Collection
    {
        $collection = Collection::make([]);
        $page = 1;
        do {
            $this->request->getRequest('fifa/ultimate-team/api/fut/item', [
                'league' => self::LEAGUE_ID,
                'page'   => $page,
            ]);

            $response = $this->request()->response()->toArray();;
            $collection->push(Arr::get($response, 'items'));
            dump('Get content, check pages: '. $page.' | '. ($test?1:$response['totalPages'])."\n");
            $page++;
        } while ($page <= ($test?1:$response['totalPages']));

        return $collection->flatten(1)->unique('baseId');
    }
}