<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Authorization\ValueObject;

final readonly class ConnectFormVO
{
    /**
     * @param array<string, string> $fields
     */
    public function __construct(
        private string $url,
        private array $fields,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return array<string, string>
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
