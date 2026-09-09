<?php

namespace BMM\DotyposSdk\Customer;

use BMM\DotyposSdk\Customer\DTO\CustomerDTO;
use BMM\DotyposSdk\Customer\DTO\CustomersDTO;
use BMM\DotyposSdk\Customer\ValueObject\CustomerVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;

trait CustomerTrait
{
    /**
     * Retrieves a customer from the server based on the provided ID.
     *
     * @param int $id The ID of the customer to retrieve.
     * @return CustomerDTO The retrieved customer object.
     * @throws \Exception
     */
    public function getCustomer(int $id): CustomerDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getCustomer()->getUrl(),
            path: $this->getEndpoint()->getCustomer()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->getCustomer()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $customer = $this->deserialize($response->data, CustomerDTO::class, $response->etag);

        return $customer;
    }

    /**
     * Retrieves a list of customers from the server.
     *
     * @param ?PaginationVO $pagination Pagination options for the request.
     * @return CustomersDTO The retrieved list of customers.
     * @throws \Exception
     */
    public function getCustomers(?PaginationVO $pagination): CustomersDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getCustomers()->getUrl(),
            path: $this->getEndpoint()->getCustomers()->getPath(),
            requestsMethod: $this->getEndpoint()->getCustomers()->getRequestsMethod(),
            pagination: $pagination
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $customers = $this->deserialize($response->data, CustomersDTO::class, $response->etag);

//        TODO add support for page, limit, filter, sort
        return $customers;
    }

    /**
     * Creates a new customer on the server.
     *
     * @param CustomerVO $payload The payload containing the customer data.
     * @return CustomerDTO The newly created customer object.
     * @throws \Exception
     */
    public function createCustomer(CustomerVO $payload): CustomerDTO
    {
        $customers = $this->createCustomers([$payload]);

        return $customers[0];
    }

    /**
     * Creates customers on the server based on the provided payload.
     *
     * @param CustomerVO[] $payload An array of customer data to create.
     * @return CustomerDTO[] An array of created customer objects.
     * @throws \Exception if there is an error during the request.
     */
    public function createCustomers(array $payload): array
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->createCustomers()->getUrl(),
            path: $this->getEndpoint()->createCustomers()->getPath(),
            requestsMethod: $this->getEndpoint()->createCustomers()->getRequestsMethod(),
            data: $this->serialize($payload)
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserializeMany($response->data, CustomerDTO::class);
    }

    /**
     * Replaces an existing customer with the provided payload.
     *
     * @param CustomerVO $payload The payload of the customer data to replace.
     * @param string $eTag The ETag header value used for optimistic concurrency control.
     * @return CustomerDTO The deserialized response data as a CustomerDTO object.
     * @throws \Exception
     */
    public function replaceCustomer(CustomerVO $payload, string $eTag): CustomerDTO
    {
        $id = $payload->getId();
        if ($id === null) {
            throw new \Exception('The id field is required.');
        }

        $request = new RequestVO(
            uri: $this->getEndpoint()->replaceCustomer()->getUrl(),
            path: $this->getEndpoint()->replaceCustomer()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->replaceCustomer()->getRequestsMethod(),
            data: $this->serialize($payload),
            eTag: $eTag
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, CustomerDTO::class);
    }

    /**
     * Replaces existing customers with the provided payload.
     *
     * @param CustomerVO[] $payload An array of customer data to replace.
     * @param string $eTag The ETag header value used for optimistic concurrency control.
     * @return CustomerDTO[] An array of deserialized response data as CustomerDTO objects.
     * @throws \Exception
     */
    public function replaceCustomers(array $payload, string $eTag): array
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->replaceCustomer()->getUrl(),
            path: $this->getEndpoint()->replaceCustomer()->getPath(),
            requestsMethod: $this->getEndpoint()->replaceCustomer()->getRequestsMethod(),
            data: $this->serialize($payload),
            eTag: $eTag
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserializeMany($response->data, CustomerDTO::class);
    }

    /**
     * Deletes a customer with the provided ID.
     *
     * @param int $id The ID of the customer to delete.
     * @return CustomerDTO The deserialized response data as a CustomerDTO object.
     * @throws \Exception
     */
    public function deleteCustomer(int $id): CustomerDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->deleteCustomers()->getUrl(),
            path: $this->getEndpoint()->deleteCustomers()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->deleteCustomers()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, CustomerDTO::class);
    }
}
