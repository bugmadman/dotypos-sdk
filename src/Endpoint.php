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

    /**
     * @var array<string, array{0: string, 1: string, 2: ?string}> name => [url, requestsMethod, path]
     */
    private const ENDPOINTS = [
        'accessToken' => [self::TOKEN_URI, self::REQUESTS_METHOD_POST, null],
        'getCustomer' => [self::API_URL, self::REQUESTS_METHOD_GET, 'customers'],
        'getCustomers' => [self::API_URL, self::REQUESTS_METHOD_GET, 'customers'],
        'createCustomers' => [self::API_URL, self::REQUESTS_METHOD_POST, 'customers'],
        'replaceCustomer' => [self::API_URL, self::REQUESTS_METHOD_PUT, 'customers'],
        'deleteCustomers' => [self::API_URL, self::REQUESTS_METHOD_DELETE, 'customers'],
        'createDiscountGroups' => [self::API_URL, self::REQUESTS_METHOD_POST, 'discount-groups'],
        'getDiscountGroup' => [self::API_URL, self::REQUESTS_METHOD_GET, 'discount-groups'],
        'deleteDiscountGroup' => [self::API_URL, self::REQUESTS_METHOD_DELETE, 'discount-groups'],
        'getDiscountGroups' => [self::API_URL, self::REQUESTS_METHOD_GET, 'discount-groups'],
        'replaceDiscountGroup' => [self::API_URL, self::REQUESTS_METHOD_PUT, 'discount-groups'],
        'replaceDiscountGroups' => [self::API_URL, self::REQUESTS_METHOD_PUT, 'discount-groups'],
        'getOrder' => [self::API_URL, self::REQUESTS_METHOD_GET, 'orders'],
        'getOrders' => [self::API_URL, self::REQUESTS_METHOD_GET, 'orders'],
        'getOrderItem' => [self::API_URL, self::REQUESTS_METHOD_GET, 'order-items'],
        'getOrderItems' => [self::API_URL, self::REQUESTS_METHOD_GET, 'order-items'],
        'getReservation' => [self::API_URL, self::REQUESTS_METHOD_GET, 'reservations'],
        'getReservations' => [self::API_URL, self::REQUESTS_METHOD_GET, 'reservations'],
        'createReservations' => [self::API_URL, self::REQUESTS_METHOD_POST, 'reservations'],
        'replaceReservation' => [self::API_URL, self::REQUESTS_METHOD_PUT, 'reservations'],
        'replaceReservations' => [self::API_URL, self::REQUESTS_METHOD_PUT, 'reservations'],
        'deleteReservation' => [self::API_URL, self::REQUESTS_METHOD_DELETE, 'reservations'],
        'getTables' => [self::API_URL, self::REQUESTS_METHOD_GET, 'tables'],
        'getBranches' => [self::API_URL, self::REQUESTS_METHOD_GET, 'branches'],
        'getWebhooks' => [self::API_URL, self::REQUESTS_METHOD_GET, 'webhooks'],
        'getWarehouses' => [self::API_URL, self::REQUESTS_METHOD_GET, 'warehouses'],
        'registerWebhooks' => [self::API_URL, self::REQUESTS_METHOD_POST, 'webhooks'],
        'deleteWebhook' => [self::API_URL, self::REQUESTS_METHOD_DELETE, 'webhooks'],
    ];

    public function get(string $name): EndpointVO
    {
        [$url, $requestsMethod, $path] = self::ENDPOINTS[$name];

        return new EndpointVO(
            url: $url,
            requestsMethod: $requestsMethod,
            path: $path
        );
    }

//    accessToken to AutorizationEndpoint
    public function accessToken(): EndpointVO
    {
        return $this->get('accessToken');
    }

    public function getCustomer(): EndpointVO
    {
        return $this->get('getCustomer');
    }

    public function getCustomers(): EndpointVO
    {
        return $this->get('getCustomers');
    }

    public function createCustomers(): EndpointVO
    {
        return $this->get('createCustomers');
    }

    public function replaceCustomer(): EndpointVO
    {
        return $this->get('replaceCustomer');
    }

    public function deleteCustomers(): EndpointVO
    {
        return $this->get('deleteCustomers');
    }

    public function createDiscountGroups(): EndpointVO
    {
        return $this->get('createDiscountGroups');
    }

    public function getDiscountGroup(): EndpointVO
    {
        return $this->get('getDiscountGroup');
    }

    public function deleteDiscountGroup(): EndpointVO
    {
        return $this->get('deleteDiscountGroup');
    }

    public function getDiscountGroups(): EndpointVO
    {
        return $this->get('getDiscountGroups');
    }

    public function replaceDiscountGroup(): EndpointVO
    {
        return $this->get('replaceDiscountGroup');
    }

    public function replaceDiscountGroups(): EndpointVO
    {
        return $this->get('replaceDiscountGroups');
    }

    public function getOrder(): EndpointVO
    {
        return $this->get('getOrder');
    }

    public function getOrders(): EndpointVO
    {
        return $this->get('getOrders');
    }

    public function getOrderItem(): EndpointVO
    {
        return $this->get('getOrderItem');
    }

    public function getOrderItems(): EndpointVO
    {
        return $this->get('getOrderItems');
    }

    public function getReservation(): EndpointVO
    {
        return $this->get('getReservation');
    }

    public function getReservations(): EndpointVO
    {
        return $this->get('getReservations');
    }

    public function createReservations(): EndpointVO
    {
        return $this->get('createReservations');
    }

    public function replaceReservation(): EndpointVO
    {
        return $this->get('replaceReservation');
    }

    public function replaceReservations(): EndpointVO
    {
        return $this->get('replaceReservations');
    }

    public function deleteReservation(): EndpointVO
    {
        return $this->get('deleteReservation');
    }

    public function getTables(): EndpointVO
    {
        return $this->get('getTables');
    }

    public function getBranches(): EndpointVO
    {
        return $this->get('getBranches');
    }

    public function getWebhooks(): EndpointVO
    {
        return $this->get('getWebhooks');
    }

    public function getWarehouses(): EndpointVO
    {
        return $this->get('getWarehouses');
    }

    public function registerWebhooks(): EndpointVO
    {
        return $this->get('registerWebhooks');
    }

    public function deleteWebhook(): EndpointVO
    {
        return $this->get('deleteWebhook');
    }
}
