<?php

namespace App\Connector\Exception;

use Psr\Http\Message\ResponseInterface;

class Runtime extends \Exception
{
    private $response;

    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param ResponseInterface $response
     */
    private function __construct(
        ResponseInterface $response,
        string $message = "",
        int $code = 0,
        \Throwable $previous = null
    ) {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param ResponseInterface $response
     * @return Runtime
     */
    public static function responseCode(\Psr\Http\Message\ResponseInterface $response): Runtime
    {
        return new self(
            $response,
            "Unexpected response code: {$response->getStatusCode()}"
        );
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}