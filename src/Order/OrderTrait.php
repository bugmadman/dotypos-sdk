<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Order;

use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\FilterVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\SortVO;
use BMM\DotyposSdk\Order\DTO\OrdersDTO;
use BMM\DotyposSdk\Order\DTO\OrderDTO;

trait OrderTrait
{
    public function getOrder(int $id): OrderDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getOrder()->getUrl(),
            path: $this->getEndpoint()->getOrder()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->getOrder()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, OrderDTO::class, $response->etag);
    }

    public function getOrders(?PaginationVO $pagination = null, ?FilterVO $filter = null, ?SortVO $sort = null): OrdersDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getOrders()->getUrl(),
            path: $this->getEndpoint()->getOrders()->getPath(),
            requestsMethod: $this->getEndpoint()->getOrders()->getRequestsMethod(),
            pagination: $pagination,
            filter: $filter,
            sort: $sort,
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, OrdersDTO::class, $response->etag);
    }
}
