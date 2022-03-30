<?php

namespace App\Foundation\AbstractModules\IntegrationManager\Reqponses;

use Psr\Http\Message\ResponseInterface;

/**
 *
 */
abstract class AbstractHttpIntegrationResponse
{
    /**
     * @var ResponseInterface $response
     */
    protected $response;

    /**
     * @var
     */
    protected $body;

    /**
     * WayForPayResponse constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $this->setData();
    }

    /**
     * @return void
     */
    protected function setData()
    {
        $this->body = $this->response->getBody();
    }

    /**
     * @return mixed
     */
    public function toObject()
    {
        return json_decode($this->body);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->body, true);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return str_replace('"', '', (string) $this->body);
    }
}