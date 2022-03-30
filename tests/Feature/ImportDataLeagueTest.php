<?php

namespace Tests\Feature;

use App\Services\ClubService;
use Illuminate\Container\Container;
use Tests\TestCase;

class ImportDataLeagueTest extends TestCase
{

    public function test_import_first_page(){
        $response = $this->artisan('league:import --test');
        $response->assertExitCode(0);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_check_clubs()
    {
        $response = $this->get(route('club.index'));
        $response->assertStatus(200);
        $this->clearDB();
    }

    protected function clearDB(){
        /** @var ClubService $clubService */
        $clubService = Container::getInstance()->make(ClubService::class);
        $clubService->clearDB();
    }
}
