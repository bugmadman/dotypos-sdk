<?php

namespace BMM\DotyposSdk\Infrastructure\ValueObject;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatorFactory
{
    private static ?ValidatorInterface $validator = null;

    public static function get(): ValidatorInterface
    {
        return self::$validator ??= Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
    }
}
