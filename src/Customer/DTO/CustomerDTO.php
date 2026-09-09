<?php

namespace BMM\DotyposSdk\Customer\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;

final class CustomerDTO extends DTO
{
    public int $_cloudId;
    public ?int $_discountGroupId = null;
    public ?int $_sellerId = null;
    public string $addressLine1;
    public ?string $addressLine2 = null;
    public string $barcode;
    public ?string $birthday = null;
    public ?string $city = null;
    public string $companyId;
    public string $companyName;
    public ?string $country = null;
    public string $created;
    public bool $deleted;
    public bool $display;
    public string $email;
    public ?string $expireDate = null;
    public ?string $externalId = null;
    public string $firstName;
    public string $flags;
    public string $headerPrint;
    public string $hexColor;
    public int $id;
    public string $internalNote;
    public string $lastName;
    public int $modifiedBy;
    public ?string $note = null;
    public string $phone;
    public float $points;
    /** @var string[] */
    public array $tags;
    public string $vatId;
    public string $versionDate;
    public string $zip;
}
