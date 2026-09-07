<?php

namespace BMM\DotyposSdk\Infrastructure\ValueObject;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

readonly class ValueObject
{
    protected function validate(): void
    {
        $violations = ValidatorFactory::get()->validate($this);

        if (0 !== count($violations)) {
            $prepareErrors = $this->prepareErrors($violations);
            $responseErrors = 'Failed validation: ';
            foreach ($prepareErrors as $index => $prepareError) {
                $responseErrors .= $prepareError->getParameter() . ': ' . $prepareError->getErrorMessage();
                if (count($prepareErrors) !== $index) {
                    $responseErrors .= ' ';
                }
            }

            throw new ValidationException($responseErrors);
        }
    }

    /**
     * @param ConstraintViolationListInterface $payload
     * @return ValidationError[]
     */
    private function prepareErrors(ConstraintViolationListInterface $payload): array
    {
        $mappedErrors = [];
        foreach ($payload as $index => $errorObject) {
            /** @var ConstraintViolationInterface $error */
            $error = $payload[$index];
            $mappedErrors[] = new ValidationError($error->getPropertyPath(), $error->getMessage(), $error);
        }

        return $mappedErrors;
    }
}
