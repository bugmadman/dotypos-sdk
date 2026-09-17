<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

final readonly class SortVO
{
    /**
     * @param SortFieldVO[] $fields
     */
    public function __construct(
        private array $fields,
    ) {
    }

    public function toQueryValue(): string
    {
        return implode(',', array_map(
            static fn (SortFieldVO $field): string => $field->toQueryFragment(),
            $this->fields
        ));
    }
}
