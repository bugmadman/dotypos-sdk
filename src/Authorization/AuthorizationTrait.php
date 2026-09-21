<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Authorization;

use BMM\DotyposSdk\Authorization\DTO\AccessTokenDTO;
use BMM\DotyposSdk\Authorization\ValueObject\AccessTokenVO;
use BMM\DotyposSdk\Authorization\ValueObject\ConnectFormVO;
use BMM\DotyposSdk\Authorization\ValueObject\ConnectUrlVO;
use BMM\DotyposSdk\Infrastructure\DataTransformer\DeserializerTrait;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\AuthorizationRequestVO;

trait AuthorizationTrait
{
    use DeserializerTrait;

    /**
     * @deprecated Documented Step 1 flow is now the signed `POST /client/connect/v2`
     * (see {@see self::getConnectFormV2()}). Kept unchanged since Dotypos still accepts
     * this GET-style URL; not removed.
     */
    public function getConnectUri(ConnectUrlVO $payload): string
    {
        return \sprintf(
            '%s?client_id=%s&client_secret=%s&scope=%s&redirect_uri=%s%s',
            $this->getEndpoint()::CONNECT_URI,
            $payload->getClientId(),
            $payload->getClientSecret(),
            $payload->getScope(),
            $payload->getRedirectUri(),
            $payload->getState() !== null ? '&state=' . $payload->getState() : ''
        );
    }

    /**
     * Builds the signed `POST /client/connect/v2` form per Dotypos's documented Step 1
     * (Guides → Authorization): `signature` is `hex(HMAC_SHA256(key: client_secret,
     * message: String(timestamp)))`. Not a REST call — the caller must render/submit
     * the returned fields as a form (or redirect the browser to one) themselves.
     *
     * Implemented per specification only — not verified against a live Dotypos
     * request (no account access available at implementation time). `$timestamp` is
     * exposed for deterministic testing; callers normally omit it.
     */
    public function getConnectFormV2(ConnectUrlVO $payload, ?int $timestamp = null): ConnectFormVO
    {
        $timestamp ??= \time();
        $signature = \hash_hmac('sha256', (string) $timestamp, $payload->getClientSecret());

        $fields = [
            'client_id' => $payload->getClientId(),
            'timestamp' => (string) $timestamp,
            'signature' => $signature,
            'scope' => $payload->getScope() ?? '*',
            'redirect_uri' => $payload->getRedirectUri(),
        ];

        if ($payload->getState() !== null) {
            $fields['state'] = $payload->getState();
        }

        return new ConnectFormVO(
            url: $this->getEndpoint()::CONNECT_URI_V2,
            fields: $fields,
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
