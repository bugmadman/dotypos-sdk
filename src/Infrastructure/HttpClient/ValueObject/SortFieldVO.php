<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

final readonly class SortFieldVO
{
    public function __construct(
        private string $attribute,
        private SortDirection $direction = SortDirection::Ascending,
    ) {
    }

    public function toQueryFragment(): string
    {
        return $this->direction === SortDirection::Descending ? '-' . $this->attribute : $this->attribute;
    }
}
