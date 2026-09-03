<?php

namespace BMM\DotyposSdk;

final class Endpoint
{
//    TODO convert to ENUM
    public const CONNECT_URI = 'https://admin.dotykacka.cz/client/connect';
    private const TOKEN_URI = 'https://api.dotykacka.cz/v2/signin/token';
    private const REQUESTS_METHOD_POST = 'POST';
    private const REQUESTS_METHOD_GET = 'GET';
    private const REQUESTS_METHOD_PUT = 'PUT';
    private const REQUESTS_METHOD_DELETE = 'DELETE';
    private const API_URL = 'https://api.dotykacka.cz/v2/clouds/';

//    accessToken to AutorizationEndpoint
    public function accessToken(): EndpointVO
    {
        return new EndpointVO(
            url: self::TOKEN_URI,
            requestsMethod: self::REQUESTS_METHOD_POST
        );
    }

    public function getCustomer(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'customers'
        );
    }

    public function getCustomers(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'customers'
        );
    }

    public function createCustomers(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_POST,
            path: 'customers'
        );
    }

    public function replaceCustomer(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_PUT,
            path: 'customers'
        );
    }

    public function deleteCustomers(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_DELETE,
            path: 'customers'
        );
    }

    public function createDiscountGroups(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_POST,
            path: 'discount-groups'
        );
    }

    public function getDiscountGroup(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'discount-groups'
        );
    }

    public function deleteDiscountGroup(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_DELETE,
            path: 'discount-groups'
        );
    }

    public function getDiscountGroups(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'discount-groups'
        );
    }

    public function replaceDiscountGroup(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_PUT,
            path: 'discount-groups'
        );
    }

    public function replaceDiscountGroups(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_PUT,
            path: 'discount-groups'
        );
    }

    public function getOrder(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'orders'
        );
    }

    public function getOrders(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'orders'
        );
    }

    public function getOrderItem(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'order-items'
        );
    }

    public function getOrderItems(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'order-items'
        );
    }

    public function getReservation(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'reservations'
        );
    }

    public function getReservations(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'reservations'
        );
    }

    public function createReservations(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_POST,
            path: 'reservations'
        );
    }

    public function replaceReservation(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_PUT,
            path: 'reservations'
        );
    }

    public function replaceReservations(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_PUT,
            path: 'reservations'
        );
    }

    public function deleteReservation(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_DELETE,
            path: 'reservations'
        );
    }

    public function getTables(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'tables'
        );
    }

    public function getBranches(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'branches'
        );
    }

    public function getWebhooks(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'webhooks'
        );
    }

    public function getWarehouses(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_GET,
            path: 'warehouses'
        );
    }

    public function registerWebhooks(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_POST,
            path: 'webhooks'
        );
    }

    public function deleteWebhook(): EndpointVO
    {
        return new EndpointVO(
            url: self::API_URL,
            requestsMethod: self::REQUESTS_METHOD_DELETE,
            path: 'webhooks'
        );
    }
}
