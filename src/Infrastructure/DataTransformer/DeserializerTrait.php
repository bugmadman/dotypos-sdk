<?php

namespace BMM\DotyposSdk\Infrastructure\DataTransformer;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait DeserializerTrait
{
    /**
     * @param string $payload
     * @param class-string<T> $dto
     * @return T|T[]
     * @template T
     */
    private function deserialize(string $payload, string $dto): object|array
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

        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($payload, $dto,  'json');
    }
}
