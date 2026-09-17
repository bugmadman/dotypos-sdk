<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Warehouse;

use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\FilterVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\SortVO;
use BMM\DotyposSdk\Warehouse\DTO\WarehousesDTO;

trait WarehouseTrait
{
    public function getWarehouses(?PaginationVO $pagination = null, ?FilterVO $filter = null, ?SortVO $sort = null): WarehousesDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getWarehouses()->getUrl(),
            path: $this->getEndpoint()->getWarehouses()->getPath(),
            requestsMethod: $this->getEndpoint()->getWarehouses()->getRequestsMethod(),
            pagination: $pagination,
            filter: $filter,
            sort: $sort,
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, WarehousesDTO::class, $response->etag);
    }
}
