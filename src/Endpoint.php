<?php

namespace BMM\DotyposSdk;

final class Endpoint
{
    public const CONNECT_URI = 'https://admin.dotykacka.cz/client/connect';
    private const TOKEN_URI = 'https://api.dotykacka.cz/v2/signin/token';
    private const API_URL = 'https://api.dotykacka.cz/v2/clouds/';

    /**
     * @var array<string, array{0: string, 1: HttpMethod, 2: string}> name => [url, requestsMethod, path]
     */
    private const ENDPOINTS = [
        EndpointName::AccessToken->value => [self::TOKEN_URI, HttpMethod::Post, ''],
        EndpointName::GetCustomer->value => [self::API_URL, HttpMethod::Get, 'customers'],
        EndpointName::GetCustomers->value => [self::API_URL, HttpMethod::Get, 'customers'],
        EndpointName::CreateCustomers->value => [self::API_URL, HttpMethod::Post, 'customers'],
        EndpointName::ReplaceCustomer->value => [self::API_URL, HttpMethod::Put, 'customers'],
        EndpointName::DeleteCustomers->value => [self::API_URL, HttpMethod::Delete, 'customers'],
        EndpointName::CreateDiscountGroups->value => [self::API_URL, HttpMethod::Post, 'discount-groups'],
        EndpointName::GetDiscountGroup->value => [self::API_URL, HttpMethod::Get, 'discount-groups'],
        EndpointName::DeleteDiscountGroup->value => [self::API_URL, HttpMethod::Delete, 'discount-groups'],
        EndpointName::GetDiscountGroups->value => [self::API_URL, HttpMethod::Get, 'discount-groups'],
        EndpointName::ReplaceDiscountGroup->value => [self::API_URL, HttpMethod::Put, 'discount-groups'],
        EndpointName::ReplaceDiscountGroups->value => [self::API_URL, HttpMethod::Put, 'discount-groups'],
        EndpointName::GetOrder->value => [self::API_URL, HttpMethod::Get, 'orders'],
        EndpointName::GetOrders->value => [self::API_URL, HttpMethod::Get, 'orders'],
        EndpointName::GetOrderItem->value => [self::API_URL, HttpMethod::Get, 'order-items'],
        EndpointName::GetOrderItems->value => [self::API_URL, HttpMethod::Get, 'order-items'],
        EndpointName::GetReservation->value => [self::API_URL, HttpMethod::Get, 'reservations'],
        EndpointName::GetReservations->value => [self::API_URL, HttpMethod::Get, 'reservations'],
        EndpointName::CreateReservations->value => [self::API_URL, HttpMethod::Post, 'reservations'],
        EndpointName::ReplaceReservation->value => [self::API_URL, HttpMethod::Put, 'reservations'],
        EndpointName::ReplaceReservations->value => [self::API_URL, HttpMethod::Put, 'reservations'],
        EndpointName::DeleteReservation->value => [self::API_URL, HttpMethod::Delete, 'reservations'],
        EndpointName::GetTables->value => [self::API_URL, HttpMethod::Get, 'tables'],
        EndpointName::GetBranches->value => [self::API_URL, HttpMethod::Get, 'branches'],
        EndpointName::GetWebhooks->value => [self::API_URL, HttpMethod::Get, 'webhooks'],
        EndpointName::GetWarehouses->value => [self::API_URL, HttpMethod::Get, 'warehouses'],
        EndpointName::RegisterWebhooks->value => [self::API_URL, HttpMethod::Post, 'webhooks'],
        EndpointName::DeleteWebhook->value => [self::API_URL, HttpMethod::Delete, 'webhooks'],
    ];

    public function get(EndpointName $name): EndpointVO
    {
        [$url, $requestsMethod, $path] = self::ENDPOINTS[$name->value];

        return new EndpointVO(
            url: $url,
            requestsMethod: $requestsMethod,
            path: $path
        );
    }

//    accessToken to AutorizationEndpoint
    public function accessToken(): EndpointVO
    {
        return $this->get(EndpointName::AccessToken);
    }

    public function getCustomer(): EndpointVO
    {
        return $this->get(EndpointName::GetCustomer);
    }

    public function getCustomers(): EndpointVO
    {
        return $this->get(EndpointName::GetCustomers);
    }

    public function createCustomers(): EndpointVO
    {
        return $this->get(EndpointName::CreateCustomers);
    }

    public function replaceCustomer(): EndpointVO
    {
        return $this->get(EndpointName::ReplaceCustomer);
    }

    public function deleteCustomers(): EndpointVO
    {
        return $this->get(EndpointName::DeleteCustomers);
    }

    public function createDiscountGroups(): EndpointVO
    {
        return $this->get(EndpointName::CreateDiscountGroups);
    }

    public function getDiscountGroup(): EndpointVO
    {
        return $this->get(EndpointName::GetDiscountGroup);
    }

    public function deleteDiscountGroup(): EndpointVO
    {
        return $this->get(EndpointName::DeleteDiscountGroup);
    }

    public function getDiscountGroups(): EndpointVO
    {
        return $this->get(EndpointName::GetDiscountGroups);
    }

    public function replaceDiscountGroup(): EndpointVO
    {
        return $this->get(EndpointName::ReplaceDiscountGroup);
    }

    public function replaceDiscountGroups(): EndpointVO
    {
        return $this->get(EndpointName::ReplaceDiscountGroups);
    }

    public function getOrder(): EndpointVO
    {
        return $this->get(EndpointName::GetOrder);
    }

    public function getOrders(): EndpointVO
    {
        return $this->get(EndpointName::GetOrders);
    }

    public function getOrderItem(): EndpointVO
    {
        return $this->get(EndpointName::GetOrderItem);
    }

    public function getOrderItems(): EndpointVO
    {
        return $this->get(EndpointName::GetOrderItems);
    }

    public function getReservation(): EndpointVO
    {
        return $this->get(EndpointName::GetReservation);
    }

    public function getReservations(): EndpointVO
    {
        return $this->get(EndpointName::GetReservations);
    }

    public function createReservations(): EndpointVO
    {
        return $this->get(EndpointName::CreateReservations);
    }

    public function replaceReservation(): EndpointVO
    {
        return $this->get(EndpointName::ReplaceReservation);
    }

    public function replaceReservations(): EndpointVO
    {
        return $this->get(EndpointName::ReplaceReservations);
    }

    public function deleteReservation(): EndpointVO
    {
        return $this->get(EndpointName::DeleteReservation);
    }

    public function getTables(): EndpointVO
    {
        return $this->get(EndpointName::GetTables);
    }

    public function getBranches(): EndpointVO
    {
        return $this->get(EndpointName::GetBranches);
    }

    public function getWebhooks(): EndpointVO
    {
        return $this->get(EndpointName::GetWebhooks);
    }

    public function getWarehouses(): EndpointVO
    {
        return $this->get(EndpointName::GetWarehouses);
    }

    public function registerWebhooks(): EndpointVO
    {
        return $this->get(EndpointName::RegisterWebhooks);
    }

    public function deleteWebhook(): EndpointVO
    {
        return $this->get(EndpointName::DeleteWebhook);
    }
}
