<?php

namespace App\Http\Controllers;

use App\Services\ClubService;
use Illuminate\Support\Collection;

/**
 *
 */
class ClubController extends Controller
{
    /**
     * @var ClubService
     */
    protected $service;

    /**
     * @param ClubService $service
     */
    public function __construct(ClubService $service)
    {
        $this->service = $service;
    }

    /**
     * @return Collection
     */
    public function index()
    {
        return $this->service->getAllForResponse();
    }
}
