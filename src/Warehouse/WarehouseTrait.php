<?php

namespace BMM\DotyposSdk\Warehouse;

use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\Warehouse\DTO\WarehousesDTO;

trait WarehouseTrait
{
    public function getWarehouses(?PaginationVO $pagination): WarehousesDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getWarehouses()->getUrl(),
            path: $this->getEndpoint()->getWarehouses()->getPath(),
            requestsMethod: $this->getEndpoint()->getWarehouses()->getRequestsMethod(),
            pagination: $pagination
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $warehouses = $this->deserialize($response->data, WarehousesDTO::class, $response->etag);

////        TODO add support for page, limit, filter, sort
        return $warehouses;
    }
}
