<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Table;

use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\FilterVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\SortVO;
use BMM\DotyposSdk\Table\DTO\TableDTO;

trait TableTrait
{
    public function getTable(int $id): TableDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getTable()->getUrl(),
            path: $this->getEndpoint()->getTable()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->getTable()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, TableDTO::class, $response->etag);
    }

    /**
     * @return TableDTO[]
     */
    public function getTables(
        ?PaginationVO $pagination = null,
        ?FilterVO $filter = null,
        ?SortVO $sort = null,
    ): array {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getTables()->getUrl(),
            path: $this->getEndpoint()->getTables()->getPath(),
            requestsMethod: $this->getEndpoint()->getTables()->getRequestsMethod(),
            pagination: $pagination,
            filter: $filter,
            sort: $sort,
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserializeMany($response->data, TableDTO::class, $response->etag);
    }
}
