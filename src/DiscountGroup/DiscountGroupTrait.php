<?php

namespace BMM\DotyposSdk\DiscountGroup;

use BMM\DotyposSdk\DiscountGroup\DTO\DiscountGroupDTO;
use BMM\DotyposSdk\DiscountGroup\DTO\DiscountGroupsDTO;
use BMM\DotyposSdk\DiscountGroup\ValueObject\DiscountGroupVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;

trait DiscountGroupTrait
{
    public function getDiscountGroup(int $id): DiscountGroupDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getDiscountGroup()->getUrl(),
            path: $this->getEndpoint()->getDiscountGroup()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->getDiscountGroup()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $discountGroup = $this->deserialize($response->data, DiscountGroupDTO::class, $response->etag);

        return $discountGroup;
    }

    public function getDiscountGroups(?PaginationVO $pagination): DiscountGroupsDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getDiscountGroups()->getUrl(),
            path: $this->getEndpoint()->getDiscountGroups()->getPath(),
            requestsMethod: $this->getEndpoint()->getDiscountGroups()->getRequestsMethod(),
            pagination: $pagination
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $discountGroups = $this->deserialize($response->data, DiscountGroupsDTO::class, $response->etag);

//        TODO add support for page, limit, filter, sor
        return $discountGroups;
    }

    /**
     * Creates a discount group.
     *
     * @param DiscountGroupVO $payload The payload containing the discount group data.
     * @return DiscountGroupDTO The created discount group.
     * @throws \Exception
     */
    public function createDiscountGroup(DiscountGroupVO $payload): DiscountGroupDTO
    {
        return $this->createDiscountGroups([$payload])[0];
    }

    /**
     * Creates discount groups.
     *
     * @param DiscountGroupVO[] $payload The payload containing the data for creating discount groups.
     * @return DiscountGroupDTO[] An array of created discount groups.
     * @throws \Exception If an error occurs during the creation of discount groups.
     */
    public function createDiscountGroups(array $payload): array
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->createDiscountGroups()->getUrl(),
            path: $this->getEndpoint()->createDiscountGroups()->getPath(),
            requestsMethod: $this->getEndpoint()->createDiscountGroups()->getRequestsMethod(),
            data: $this->serialize($payload)
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, DiscountGroupDTO::class . '[]');
    }

    public function replaceDiscountGroup(DiscountGroupVO $payload, string $eTag): DiscountGroupDTO
    {
        $id = $payload->getId();
        if ($id === null) {
            throw new \Exception('The id field is required.');
        }

        $request = new RequestVO(
            uri: $this->getEndpoint()->replaceDiscountGroup()->getUrl(),
            path: $this->getEndpoint()->replaceDiscountGroup()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->replaceDiscountGroup()->getRequestsMethod(),
            data: $this->serialize($payload),
            eTag: $eTag
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, DiscountGroupDTO::class);
    }

    /**
     * @param DiscountGroupVO[] $payload
     * @param string $eTag
     * @return DiscountGroupDTO[]
     * @throws \Exception
     */
    public function replaceDiscountGroups(array $payload, string $eTag): array
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->replaceDiscountGroups()->getUrl(),
            path: $this->getEndpoint()->replaceDiscountGroups()->getPath(),
            requestsMethod: $this->getEndpoint()->replaceDiscountGroups()->getRequestsMethod(),
            data: $this->serialize($payload),
            eTag: $eTag
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, DiscountGroupDTO::class . '[]');
    }

    public function deleteDiscountGroup(int $id)
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->deleteDiscountGroup()->getUrl(),
            path: $this->getEndpoint()->deleteDiscountGroup()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->deleteDiscountGroup()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, DiscountGroupDTO::class);
    }
}
