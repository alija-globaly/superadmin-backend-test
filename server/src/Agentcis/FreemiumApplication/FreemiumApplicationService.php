<?php

namespace Agentcis\FreemiumApplication;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use Illuminate\Support\Fluent;
use Illuminate\Validation\ValidationException;

class FreemiumApplicationService
{
    /**
     * The Forge API Key.
     *
     * @var string
     */
    public $apiKey;
    /**
     * The Guzzle HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    public $guzzle;
    /**
     * Number of seconds a request is retried.
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * @param  string $apiKey
     * @return void
     */
    public function __construct($apiKey = null)
    {
        $this->guzzle = new HttpClient([
            'headers' => [
                'Authorization' => 'agentcis-token ' . $apiKey,
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'base_uri' => env('AG_SUBSCRIPTION_API_URI'),
        ]);
    }

    public function getAllByStatus($status)
    {
        try {
            $response = $this->guzzle->get('freemium-applications?status='.$status);
            return new Fluent(json_decode($response->getBody(), true));
        } catch (ClientException $exception) {
            $response = $exception->getResponse();

            if (Response::HTTP_UNPROCESSABLE_ENTITY === $response->getStatusCode()) {
                throw ValidationException::withMessages(json_decode($response->getBody(),
                    JSON_OBJECT_AS_ARRAY)['meta']['errors']);
            }
            throw $exception;
        }
    }
    public function findById($applicationId)
    {
        try {
            $response = $this->guzzle->get('freemium-application/'.$applicationId);
            return new Fluent(json_decode($response->getBody(), true));
        } catch (ClientException $exception) {
            throw $exception;
        }
    }

    public function approve($applicationId)
    {
        try {
            $response = $this->guzzle->post('freemium-application/'.$applicationId.'/approve');
            return new Fluent(json_decode($response->getBody(), true));
        } catch (ClientException $exception) {
            throw $exception;
        }
    }
    public function changeStatus($applicationId, $status)
    {
        try {
            $response = $this->guzzle->post('freemium-application/'.$applicationId.'/'.$status);
            return new Fluent(json_decode($response->getBody(), true));
        } catch (ClientException $exception) {
            throw $exception;
        }
    }
}
