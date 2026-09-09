<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

use BMM\DotyposSdk\HttpMethod;

final readonly class AuthorizationRequestVO
{
    public function __construct(
        private string $uri,
        private HttpMethod $requestsMethod,
        private int $cloudId,
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

    public function getCloudId(): int
    {
        return $this->cloudId;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
