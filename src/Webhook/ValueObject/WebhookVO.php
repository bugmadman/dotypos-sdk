<?php

namespace BMM\DotyposSdk\Webhook\ValueObject;

final class WebhookVO
{
    public function __construct(
        //TODO from enum
        private string $method,
        private string $url,
        //TODO from enum
        private string $payloadEntity,
        private ?string $payloadVersion = 'V1',
        private ?int $_warehouseId = null,
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPayloadEntity(): string
    {
        return $this->payloadEntity;
    }

    public function getPayloadVersion(): ?string
    {
        return $this->payloadVersion;
    }

    public function getWarehouseId(): ?int
    {
        return $this->_warehouseId;
    }
}
