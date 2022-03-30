<?php

namespace App\Foundation\FootballManager\FifaManager\Requests;

use App\Foundation\AbstractModules\IntegrationManager\Requests\AbstractHttpIntegrationRequests;
use App\Foundation\FootballManager\FifaManager\Exceptions\FifaExeception;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 *
 */
class FifaRequests extends AbstractHttpIntegrationRequests
{
    /**
     *
     */
    const LEAGUE_ID = 13; // Primier League

    /**
     * @var string
     */
    protected $url = 'https://www.easports.com/';

    /**
     * @param HttpException $exception
     * @return mixed
     */
    protected function connectionException(HttpException $exception)
    {
        throw new FifaExeception(sprintf('Fifa connection exception: %s', $exception->getMessage()),
            503, $exception->getPrevious()
        );
    }

    /**
     * @param GuzzleException $exception
     * @param                 $content
     * @return mixed
     */
    protected function badResponseException(GuzzleException $exception, $content)
    {
        throw new FifaExeception(sprintf('Fifa connection exception: %s. Response: %s', $exception->getMessage(), $content),
            503,
            $exception->getPrevious()
        );
    }
}