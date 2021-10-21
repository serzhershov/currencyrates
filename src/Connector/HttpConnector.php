<?php

namespace App\Connector;

use Symfony\Component\HttpFoundation\Response;

/**
 * Extend this with properly setting required parameters: $targetUri
 */
abstract class HttpConnector
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;
    /**
     * @var string
     */
    protected $targetUri = '';

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->targetUri
        ]);
    }

    /**
     * @param $uri
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception\Runtime
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getResponse($uri): \Psr\Http\Message\ResponseInterface
    {
        $response = $this->client->request('GET', $uri);
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw Exception\Runtime::responseCode($response);
        }
        return $response;
    }

}