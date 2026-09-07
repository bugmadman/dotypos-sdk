<?php

namespace BMM\DotyposSdk\Infrastructure\DataTransformer;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait DenormalizeTrait
{
    /**
     * @param array $payload
     * @param class-string<T> $dto
     * @return T
     * @template T
     * @throws ExceptionInterface
     */
    private function denormalize(array $payload, string $dto): object
    {
        $normalizers = [
            new ObjectNormalizer(),
        ];
        $serializer = new Serializer($normalizers);

        return $serializer->denormalize($payload, $dto);
    }
}
