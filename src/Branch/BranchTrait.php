<?php

namespace BMM\DotyposSdk\Branch;

use BMM\DotyposSdk\Branch\DTO\BranchesDTO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;

trait BranchTrait
{
    public function getBranches(?PaginationVO $pagination): BranchesDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getBranches()->getUrl(),
            path: $this->getEndpoint()->getBranches()->getPath(),
            requestsMethod: $this->getEndpoint()->getBranches()->getRequestsMethod(),
            pagination: $pagination
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $branches = $this->deserialize($response->data, BranchesDTO::class);
        $branches->eTag = $response->etag;

//        TODO add support for page, limit, filter, sor
        return $branches;
    }
}