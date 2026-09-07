<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

use BMM\DotyposSdk\HttpMethod;

final readonly class RequestVO
{
    public function __construct(
        private string $uri,
        private string $path,
        private HttpMethod $requestsMethod,
        private ?string $data = null,
        private ?string $eTag = null,
        private ?PaginationVO $pagination = null,
    ) {
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRequestsMethod(): HttpMethod
    {
        return $this->requestsMethod;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function getETag(): ?string
    {
        return $this->eTag;
    }

    public function getPagination(): ?PaginationVO
    {
        return $this->pagination;
    }
}
