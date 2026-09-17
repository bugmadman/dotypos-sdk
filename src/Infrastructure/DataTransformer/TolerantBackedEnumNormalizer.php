<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\DataTransformer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class TolerantBackedEnumNormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!\is_string($data) && !\is_int($data)) {
            throw new \InvalidArgumentException(\sprintf(
                'Expected a string or int enum value, got %s.',
                get_debug_type($data)
            ));
        }

        /** @var class-string<TolerantBackedEnum> $type */
        return $type::fromApiValue((string) $data);
    }

    public function supportsDenormalization(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = [],
    ): bool {
        return is_subclass_of($type, TolerantBackedEnum::class);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [TolerantBackedEnum::class => true];
    }
}
