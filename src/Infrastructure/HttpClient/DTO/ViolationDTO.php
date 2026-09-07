<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient\DTO;

final class ViolationDTO
{
    public ?string $fieldName = null;
    public string $message;
}
