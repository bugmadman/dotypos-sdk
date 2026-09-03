<?php

namespace BMM\DotyposSdk\Infrastructure\DataTransformer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

trait SerializerTrait
{
    private function serialize(object|array $payload): string
    {
        $dateCallback = function (
            object $innerObject,
            object $outerObject,
            string $attributeName,
            string $format = null,
            array $context = [])
        : string {
            return $innerObject instanceof \DateTimeImmutable ? $innerObject->format(\DateTimeImmutable::ATOM) : '';
        };

        $defaultContext = [
            AbstractNormalizer::CALLBACKS => [
                'createdAt' => $dateCallback,
            ],
        ];

        $normalizers = [
            new DateTimeNormalizer(),
            new PropertyNormalizer(
                null,
                null,
                null,
                null,
                null,
                $defaultContext,
            )
        ];
        $encoders = [new JsonEncoder()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($payload, JsonEncoder::FORMAT);
    }
}