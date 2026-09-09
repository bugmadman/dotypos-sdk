<?php

namespace BMM\DotyposSdk\Infrastructure\DataTransformer;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait DeserializerTrait
{
    /**
     * @param class-string<T> $dto
     * @return T
     * @template T of object
     */
    private function deserialize(string $payload, string $dto, ?string $eTag = null): object
    {
        $result = $this->buildSerializer()->deserialize($payload, $dto, 'json');

        if (!$result instanceof $dto) {
            throw new \UnexpectedValueException(\sprintf(
                'Expected deserialize() to return an instance of %s, got %s.',
                $dto,
                get_debug_type($result)
            ));
        }

        if ($eTag !== null && $result instanceof DTO) {
            $result->setETag($eTag);
        }

        return $result;
    }

    /**
     * @param class-string<T> $dto
     * @return T[]
     * @template T of object
     */
    private function deserializeMany(string $payload, string $dto, ?string $eTag = null): array
    {
        $result = $this->buildSerializer()->deserialize($payload, $dto . '[]', 'json');

        if (!\is_array($result)) {
            throw new \UnexpectedValueException(\sprintf(
                'Expected deserializeMany() to return an array of %s, got %s.',
                $dto,
                get_debug_type($result)
            ));
        }

        $items = [];
        foreach ($result as $item) {
            if (!$item instanceof $dto) {
                throw new \UnexpectedValueException(\sprintf(
                    'Expected deserializeMany() to return an array of %s, got %s among its items.',
                    $dto,
                    get_debug_type($item)
                ));
            }

            if ($eTag !== null && $item instanceof DTO) {
                $item->setETag($eTag);
            }

            $items[] = $item;
        }

        return $items;
    }

    private function buildSerializer(): Serializer
    {
        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor()]);
        $normalizers = [
            new ObjectNormalizer(
                null,
                null,
                null,
                $extractor
            ),
            new ArrayDenormalizer(),
        ];

        return new Serializer($normalizers, [new JsonEncoder()]);
    }
}
