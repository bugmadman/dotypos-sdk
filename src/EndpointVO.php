<?php

namespace BMM\DotyposSdk;

final readonly class EndpointVO
{
    public function __construct(
        private string $url,
        private HttpMethod $requestsMethod,
        private string $path = '',
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRequestsMethod(): HttpMethod
    {
        return $this->requestsMethod;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
