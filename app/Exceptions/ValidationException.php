<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ValidationException extends Exception implements HttpExceptionInterface
{
    /**
     * @var Validator $validator
     */
    protected $validator;

    /**
     * @var int $code
     */
    protected $code = 422;

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->code;
    }

    public function getHeaders(): array
    {
        return [];
    }

    /**
     * ValidationException constructor.
     *
     * @param Validator      $validator
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(Validator $validator, string $message = "", int $code = 422, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->validator = $validator;
        $this->setErrorMessage();
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->code;
    }

    /**
     *
     */
    protected function setErrorMessage()
    {
        $this->message = $this->getValidator()->errors()->messages();
        if (strpos(request()->getRequestUri(), '/api') === 0 || strpos(request()->getRequestUri(), '/site') === 0) {
            $firstKey = key($this->message);
            $this->message = array_shift($this->message[$firstKey]);
        }
        return $this;
    }
}
