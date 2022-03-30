<?php

namespace App\Foundation\AbstractModules\IntegrationManager\Requests;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 *
 */
abstract class AbstractHttpIntegrationRequests
{
    /**
     * @var
     */
    protected $URI;

    /**
     * @var
     */
    protected $prefixURN;

    /**
     * @var
     */
    protected $url;

    /**
     * @var
     */
    protected $method;

    /**
     * @var
     */
    protected $options = [
        'timeout' => 15
    ];

    /**
     * @param HttpException $exception
     * @return mixed
     */
    abstract protected function connectionException(HttpException $exception);

    /**
     * @param GuzzleException $exception
     * @param                 $content
     * @return mixed
     */
    abstract protected function badResponseException(GuzzleException $exception, $content);

    /**
     * @param string $urn
     * @param array  $getParams
     * @return $this
     */
    protected function buildURI(string $urn, array $getParams = [])
    {
        $this->URI = $this->url . $this->prefixURN . $urn;
        if (!empty($getParams)) {
            $this->URI .= '?' . http_build_query($getParams);
        }
        return $this;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout)
    {
        $this->options['timeout'] = $timeout;
    }

    /**
     * @param string $URN
     * @param array  $postParams
     * @param array  $getParams
     * @return $this
     */
    public function postRequest(string $URN, array $postParams = [], array $getParams = [])
    {
        $this->method = 'POST';
        $this->buildURI($URN, $getParams);
        $this->options['json'] = $postParams;
        return $this;
    }

    /**
     * @param string $URN
     * @param array  $getParams
     * @return $this
     */
    public function getRequest(string $URN, array $getParams = [])
    {
        $this->method = 'GET';
        $this->buildURI($URN, $getParams);
        return $this;
    }

    /**
     * @return ResponseInterface|void
     */
    public function request()
    {
        $client = new HttpClient();
        try {
            return $client->request($this->method, $this->URI, $this->options);
        } catch (ConnectException $exception) {
            $this->connectionException($exception);
        } catch (GuzzleException $exception) {
            $response = $exception->getResponse();
            $content = '';
            if ($response instanceof ResponseInterface) {
                $content = $response->getBody()->getContents();
            }

            $this->badResponseException($exception, $content);
        }
    }
}