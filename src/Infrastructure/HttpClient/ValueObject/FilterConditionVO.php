<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

final readonly class FilterConditionVO
{
    public function __construct(
        private string $attribute,
        private FilterOperation $operation,
        private string $value,
    ) {
    }

    public function toQueryFragment(): string
    {
        return $this->attribute . '|' . $this->operation->value . '|' . $this->value;
    }
}
