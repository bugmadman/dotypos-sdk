<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\DataTransformer;

/**
 * A backed enum whose set of API values is documented as non-exhaustive — the API may
 * send a value with no matching case. Implementations fall back to a dedicated case
 * instead of failing deserialization.
 */
interface TolerantBackedEnum
{
    public static function fromApiValue(string $value): self;
}
