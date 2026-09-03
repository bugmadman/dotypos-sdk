<?php

namespace BMM\DotyposSdk\OrderItem;

use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\OrderItem\DTO\OrderItemDTO;
use BMM\DotyposSdk\OrderItem\DTO\OrderItemsDTO;

trait OrderItemTrait
{
    public function getOrderItem(int $id): OrderItemDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getOrderItem()->getUrl(),
            path: $this->getEndpoint()->getOrderItem()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->getOrderItem()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, OrderItemDTO::class);
    }

    public function getOrderItems(?PaginationVO $pagination): OrderItemsDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getOrderItems()->getUrl(),
            path: $this->getEndpoint()->getOrderItems()->getPath(),
            requestsMethod: $this->getEndpoint()->getOrderItems()->getRequestsMethod(),
            pagination: $pagination
        );
//        TODO add support for page, limit, filter, sor
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, OrderItemsDTO::class);
    }
}