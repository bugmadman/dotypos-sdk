<?php

namespace BMM\DotyposSdk;

use BMM\DotyposSdk\Authorization\AuthorizationTrait;
use BMM\DotyposSdk\Infrastructure\HttpClient\HttpClient;

final class AuthorizationApi
{
    use AuthorizationTrait;

    private function getEndpoint(): Endpoint
    {
        return new Endpoint();
    }

    private function getHttpClient(): HttpClient
    {
        return new HttpClient();
    }
}