<?php

namespace Agentcis\Core;

use GuzzleHttp\Client;

class AbstractResource
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get($uri)
    {
        return $this->handleResponse($this->client->get($uri));
    }

    public function create($uri, $details, $formattedResponse = true)
    {
        $response = $this->client->post($uri, [
            'json' => $details
        ]);
        return $formattedResponse ? $this->handleResponse($response) : $response;
    }

    public function update($uri, $details)
    {
        return $this->client->put($uri, [
            'json' => $details
        ]);
    }

    public function remove($uri, $details)
    {
        $response = $this->client->post($uri, [
            'json' => $details,
        ]);
        return $this->handleResponse($response);
    }

    public function delete($uri, $details)
    {
        return $this->client->delete($uri, [
            'json' => $details,
        ]);
    }

    public function handleResponse($response)
    {
        return json_decode($response->getBody(), true);
    }

    public function getResponse($uri): ?\Psr\Http\Message\ResponseInterface
    {
        return $this->client->get($uri);
    }

    public function postFormData($uri, $details, $formattedResponse = true)
    {
        $response = $this->client->post($uri, [
            'multipart' => $details,
        ]);
        return $formattedResponse ? $this->handleResponse($response) : $response;
    }
}