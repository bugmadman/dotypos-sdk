<?php

namespace BMM\DotyposSdk;

use BMM\DotyposSdk\Authorization\AuthorizationTrait;
use BMM\DotyposSdk\Infrastructure\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class AuthorizationApi
{
    use AuthorizationTrait;

    public function __construct(
        private ?HttpClientInterface $httpClient = null,
    ) {
    }

    private function getEndpoint(): Endpoint
    {
        return new Endpoint();
    }

    private function getHttpClient(): HttpClient
    {
        return new HttpClient(client: $this->httpClient);
    }
}