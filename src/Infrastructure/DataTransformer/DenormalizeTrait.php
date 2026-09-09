<?php

namespace BMM\DotyposSdk\Infrastructure\DataTransformer;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait DenormalizeTrait
{
    /**
     * @param array<string, array<string>> $payload
     * @param class-string<T> $dto
     * @return T
     * @template T of object
     * @throws ExceptionInterface
     */
    private function denormalize(array $payload, string $dto): object
    {
        $normalizers = [
            new ObjectNormalizer(),
        ];
        $serializer = new Serializer($normalizers);

        $result = $serializer->denormalize($payload, $dto);

        if (!$result instanceof $dto) {
            throw new \UnexpectedValueException(\sprintf(
                'Expected denormalize() to return an instance of %s, got %s.',
                $dto,
                get_debug_type($result)
            ));
        }

        return $result;
    }
}
