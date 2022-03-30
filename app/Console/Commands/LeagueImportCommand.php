<?php

namespace App\Console\Commands;

use App\Foundation\FootballManager\FifaManager\Services\FifaService;
use App\Services\PlayerService;
use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 *
 */
class LeagueImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'league:import {--test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from FIFA.';

    /**
     * @return int
     * @throws BindingResolutionException
     */
    public function handle()
    {
        $start_memory = memory_get_usage();
        /** @var PlayerService $playerService */
        $playerService = Container::getInstance()->make(PlayerService::class);

        /** @var FifaService $fifaService */
        $fifaService = Container::getInstance()->make(FifaService::class);
        $this->info('Get content from FIFA.');
        $collection = $fifaService->getCollection($this->option('test'));

        $this->info('Import Collection to DB');
        $playerService->import($collection);

        $this->info('Done! Memory usage: ',memory_get_usage() - $start_memory);
    }
}
