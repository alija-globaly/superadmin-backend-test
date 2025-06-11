<?php

namespace Agentcis\Tenant;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use Illuminate\Support\Fluent;
use Illuminate\Validation\ValidationException;
use Agentcis\Tenant\DTOs\InvitationDTO;
use Illuminate\Support\Facades\Log;

class TenantInvitationService
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

    public function send(InvitationDTO $invitationDTO)
    {
        Log::info("Sending tenant invitation: ", [
            'invitationInfo' => $invitationDTO->toArray()
        ]);

        try {
            $response = $this->guzzle->post('tenants/external-invite', [
                'json' => $invitationDTO->toArray(),
            ]);
            return new Fluent(json_decode($response->getBody(), true));
        } catch (ClientException $exception) {
            $response = $exception->getResponse();

            Log::error("Tenant invitation error: " . $exception->getMessage(), ['trace' => $exception->getTraceAsString()]);

            if (Response::HTTP_UNPROCESSABLE_ENTITY === $response->getStatusCode()) {
                $responseBody = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);
                
                $errors = [];
                if (isset($responseBody['meta']['errors'])) {
                    $errors = $responseBody['meta']['errors'];
                } elseif (isset($responseBody['errors'])) {
                    $errors = $responseBody['errors'];
                } elseif (isset($responseBody['message'])) {
                    $errors = ['general' => [$responseBody['message']]];
                } else {
                    $errors = ['general' => ['An validation error occurred']];
                }
                
                throw ValidationException::withMessages($errors);
            }

            throw $exception;
        }
    }

    public function delete($invitationId)
    {
        try {
            $response = $this->guzzle->delete('tenants/external-invite/' . $invitationId);
            return new Fluent(json_decode($response->getBody(), true));
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            Log::error("Tenant invitation delete error: " . $exception->getMessage(), ['trace' => $exception->getTraceAsString()]);

            if (Response::HTTP_UNPROCESSABLE_ENTITY === $response->getStatusCode()) {
                $responseBody = json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY);
                
                $errors = [];
                if (isset($responseBody['meta']['errors'])) {
                    $errors = $responseBody['meta']['errors'];
                } elseif (isset($responseBody['errors'])) {
                    $errors = $responseBody['errors'];
                } elseif (isset($responseBody['message'])) {
                    $errors = ['general' => [$responseBody['message']]];
                } else {
                    $errors = ['general' => ['An validation error occurred']];
                }
                
                throw ValidationException::withMessages($errors);
            }

            throw $exception;
        }
    }
}
