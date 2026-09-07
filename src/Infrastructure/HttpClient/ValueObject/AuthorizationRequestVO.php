<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

use BMM\DotyposSdk\HttpMethod;

final readonly class AuthorizationRequestVO
{
    public function __construct(
        private string $uri,
        private HttpMethod $requestsMethod,
        private string $cloudId,
        private string $user,
    ) {
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getRequestsMethod(): HttpMethod
    {
        return $this->requestsMethod;
    }

    public function getCloudId(): string
    {
        return $this->cloudId;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
