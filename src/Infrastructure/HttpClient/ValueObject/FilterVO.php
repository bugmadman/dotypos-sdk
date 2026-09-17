<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

final readonly class FilterVO
{
    /**
     * @param FilterConditionVO[] $conditions
     */
    public function __construct(
        private array $conditions,
    ) {
    }

    public function toQueryValue(): string
    {
        return implode(';', array_map(
            static fn (FilterConditionVO $condition): string => $condition->toQueryFragment(),
            $this->conditions
        ));
    }
}
