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
     * @param string $payload
     * @param class-string<T> $dto
     * @return T|T[]
     * @template T
     */
    private function deserialize(string $payload, string $dto, ?string $eTag = null): object|array
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

        $result = $serializer->deserialize($payload, $dto, 'json');

        if ($eTag !== null && $result instanceof DTO) {
            $result->setETag($eTag);
        }

        return $result;
    }
}
