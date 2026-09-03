<?php

namespace BMM\DotyposSdk\Authorization;

use BMM\DotyposSdk\Authorization\DTO\AccessTokenDTO;
use BMM\DotyposSdk\Authorization\ValueObject\AccessTokenVO;
use BMM\DotyposSdk\Authorization\ValueObject\ConnectUrlVO;
use BMM\DotyposSdk\Infrastructure\DataTransformer\DeserializerTrait;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\AuthorizationRequestVO;

trait AuthorizationTrait
{
    use DeserializerTrait;

    public function getConnectUri(ConnectUrlVO $payload): string
    {
        return \sprintf('%s?client_id=%s&client_secret=%s&scope=%s&redirect_uri=%s%s',
            $this->getEndpoint()::CONNECT_URI,
            $payload->getClientId(),
            $payload->getClientSecret(),
            $payload->getScope(),
            $payload->getRedirectUri(),
            $payload->getState() !== null ? '&state=' . $payload->getState() : ''
        );
    }

    public function getAccessToken(AccessTokenVO $payload): AccessTokenDTO
    {
        $authorizationRequest = new AuthorizationRequestVO(
            uri: $this->getEndpoint()->accessToken()->getUrl(),
            requestsMethod: $this->getEndpoint()->accessToken()->getRequestsMethod(),
            cloudId: $payload->getCloudId(),
            user: $payload->getUser()
        );
        $response = $this->getHttpClient()->sendAuthorizationRequest($authorizationRequest);

        return $this->deserialize($response, AccessTokenDTO::class);
    }
}