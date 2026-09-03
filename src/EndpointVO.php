<?php

namespace BMM\DotyposSdk;

final class EndpointVO
{
    public function __construct(
        private string $url,
        private string $requestsMethod,
        private ?string $path = null,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getRequestsMethod(): string
    {
        return $this->requestsMethod;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
}