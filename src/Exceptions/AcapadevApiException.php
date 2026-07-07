<?php

namespace Acapadev\Sdk\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class AcapadevApiException extends Exception
{
    /**
     * @var \Illuminate\Http\Client\Response|null
     */
    public $response;

    public function __construct(string $message, int $code = 0, ?Response $response = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * Create an exception from a failed HTTP response.
     */
    public static function fromResponse(Response $response)
    {
        $message = "Acapadev API Error [{$response->status()}]: " . $response->body();
        return new static($message, $response->status(), $response);
    }
}
