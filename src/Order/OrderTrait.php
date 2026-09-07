<?php

namespace BMM\DotyposSdk\Order;

use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\Order\DTO\OrdersDTO;
use BMM\DotyposSdk\Order\DTO\OrderDTO;

trait OrderTrait
{
    public function getOrder(int $id)
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getOrder()->getUrl(),
            path: $this->getEndpoint()->getOrder()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->getOrder()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $order = $this->deserialize($response->data, OrderDTO::class, $response->etag);

        return $order;
    }

    public function getOrders(?PaginationVO $pagination): OrdersDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getOrders()->getUrl(),
            path: $this->getEndpoint()->getOrders()->getPath(),
            requestsMethod: $this->getEndpoint()->getOrders()->getRequestsMethod(),
            pagination: $pagination
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $orders = $this->deserialize($response->data, OrdersDTO::class, $response->etag);

//        TODO add support for page, limit, filter, sor
        return $orders;
    }
}