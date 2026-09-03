<?php

namespace BMM\DotyposSdk\Customer\ValueObject;

use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final class CustomerVO extends ValueObject
{
    public function __construct(
        private string $lastName,
        private ?string $addressLine1 = '',
        private ?string $barcode = '',
        private ?string $companyId = '',
        private ?string $companyName = '',
        private ?bool $display = true,
        private ?string $email = '',
        private ?string $firstName = '',
        private ?string $headerPrint = '',
        private ?string $hexColor = '#000000', // start with # add validator,
        private ?string $internalNote = '',
        private ?string $phone = '',
        private ?float $points = 0,
        private ?array $tags = [],
        private ?string $vatId = '', // DIČ validate !!!!CZ27082440
        private ?string $zip = '',
        private ?int $flags = 0,
        private ?int $id = null,
        private ?bool $deleted = false,
    ) {
        $this->validate($this);
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function getCompanyId(): ?string
    {
        return $this->companyId;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getDisplay(): ?bool
    {
        return $this->display;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getHeaderPrint(): ?string
    {
        return $this->headerPrint;
    }

    public function getHexColor(): ?string
    {
        return $this->hexColor;
    }

    public function getInternalNote(): ?string
    {
        return $this->internalNote;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function getVatId(): ?string
    {
        return $this->vatId;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function getFlags(): ?int
    {
        return $this->flags;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }
}

//
//Customer schema
//id long?
//    Customer ID - cannot be null in PUT/PATCH methods
//📶 EQUALS,ENUM
//_cloudId integer
//Cloud ID
//_discountGroupId long?
//    Discount group ID
//📶 EQUALS,ENUM
//_sellerId long?
//Seller ID
//📶 EQUALS,ENUM
//addressLine1 string(180)
//Address line 1
//📶 STRING
//addressLine2 string?(180)
//    Address line 2
//📶 STRING
//barcode string(50)
//Bar code
//📶 EQUALS,ENUM
//birthday timestamp?
//The date of birth
//city string?(255)
//    City
//📶 EQUALS,STRING
//companyId string(255)
//Customer company ID (CZ: IČO, PL: REGON)
//📶 ENUM
//companyName string(180) [1]
//Customer company name
//📶 STRING 🔽 BOTH
//country string?(10)
//    Country code
//📶 STRING
//created timestamp?
//    Customer created date and time
//📶 EQUALS, ENUM, NUMBER 🔽 BOTH
//deleted boolean
//Customer deleted - cannot be true in POST/PUT/PATCH methods
//📶 EQUALS, ENUM 🔽 BOTH
//display boolean
//Customer displayed
//📶 EQUALS, ENUM 🔽 BOTH
//email string(100)
//E-mail address
//📶 STRING
//expireDate timestamp?
//    Customer expire date and time
//📶 EQUALS, ENUM, NUMBER 🔽 BOTH
//externalId string?(256)
//    External ID
//📶 EQUALS,ENUM
//firstName string(180) [1]
//First name
//📶 STRING 🔽 BOTH
//flags long
//Customer flags
//📶 BITS
//headerPrint string(256)
//Header for printing
//           hexColor string(7)
//Product color
//internalNote string(1000)
//Internal note
//lastName string(180) [1]
//Last name
//📶 STRING 🔽 BOTH
//modifiedBy string?(32)
//    Customer modified by
//note string?(500)
//    Customer note
//phone string(20)
//Phone
//📶 STRING
//points double
//Customer points - must be greater than or equal to 0
//📶 NUMBER
//tags string[](255)
//Tags for a customer
//         📶 EQUALS, ENUM
//vatId string(255)
//Customer VAT ID (CZ: DIČ, PL: NIP). Validation regex.
//versionDate timestamp?
//    Last modification date and time
//📶 EQUALS, ENUM, NUMBER 🔽 BOTH
//zip string(20)
//ZIP code
//📶 STRING
//[1] Properties firstName, lastName and companyName  must not be blank. At least one of these properties must contain a non-blank value.