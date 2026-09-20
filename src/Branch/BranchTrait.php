<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Branch;

use BMM\DotyposSdk\Branch\DTO\BranchDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\FilterVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\SortVO;

trait BranchTrait
{
    public function getBranch(int $id): BranchDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getBranch()->getUrl(),
            path: $this->getEndpoint()->getBranch()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->getBranch()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, BranchDTO::class, $response->etag);
    }

    /**
     * @return BranchDTO[]
     */
    public function getBranches(
        ?PaginationVO $pagination = null,
        ?FilterVO $filter = null,
        ?SortVO $sort = null,
    ): array {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getBranches()->getUrl(),
            path: $this->getEndpoint()->getBranches()->getPath(),
            requestsMethod: $this->getEndpoint()->getBranches()->getRequestsMethod(),
            pagination: $pagination,
            filter: $filter,
            sort: $sort,
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserializeMany($response->data, BranchDTO::class, $response->etag);
    }
}
