<?php

namespace BMM\DotyposSdk\Webhook;

use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\Webhook\DTO\WebhookDTO;
use BMM\DotyposSdk\Webhook\ValueObject\WebhookVO;

trait WebhookTrait
{
    public function registerWebhooks(WebhookVO $payload): WebhookDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->registerWebhooks()->getUrl(),
            path: $this->getEndpoint()->registerWebhooks()->getPath(),
            requestsMethod: $this->getEndpoint()->registerWebhooks()->getRequestsMethod(),
            data: $this->serialize($payload)
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, WebhookDTO::class);
    }

    /**
     * @return WebhookDTO[]
     * @throws \Exception
     */
    public function getWebhooks(): array
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getWebhooks()->getUrl(),
            path: $this->getEndpoint()->getWebhooks()->getPath(),
            requestsMethod: $this->getEndpoint()->getWebhooks()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserializeMany($response->data, WebhookDTO::class);
    }

    public function deleteWebhook(int $id): WebhookDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->deleteWebhook()->getUrl(),
            path: $this->getEndpoint()->deleteWebhook()->getPath()  . '/' . $id,
            requestsMethod: $this->getEndpoint()->deleteWebhook()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, WebhookDTO::class);
    }
}
