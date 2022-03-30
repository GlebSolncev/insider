<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 *
 */
class IntegrationHttpException extends HttpException implements HttpExceptionInterface
{
    /**
     * @param string         $message
     * @param                $code
     * @param Throwable|null $previous
     * @param                $headers
     */
    public function __construct(string $message = '', $code = 503, Throwable $previous = null, $headers = [])
    {
        parent::__construct($code, $message, $previous, $headers, $code);
    }
}