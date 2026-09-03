<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

class AuthorizationRequestVO
{
    public function __construct(
        private string $uri,
        private string $requestsMethod,
        private string $cloudId,
        private string $user,
    ) {
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getRequestsMethod(): string
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

//>getUser(),
//],
//'json' => ['_cloudId' => $this->getCloudId()],
}