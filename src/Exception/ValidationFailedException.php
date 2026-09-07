<?php

namespace BMM\DotyposSdk\Exception;

use BMM\DotyposSdk\Infrastructure\HttpClient\DTO\ViolationDTO;

final class ValidationFailedException extends DotyposException
{
    /**
     * @param ViolationDTO[] $violations
     */
    public function __construct(
        string $message,
        private readonly array $violations,
    ) {
        parent::__construct($message);
    }

    /**
     * @return ViolationDTO[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
