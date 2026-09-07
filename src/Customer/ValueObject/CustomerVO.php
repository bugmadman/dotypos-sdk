<?php

namespace BMM\DotyposSdk\Customer\ValueObject;

use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CustomerVO extends ValueObject
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
        #[Assert\Regex('/^#[0-9A-Fa-f]{6}$/')]
        private ?string $hexColor = '#000000',
        private ?string $internalNote = '',
        private ?string $phone = '',
        private ?float $points = 0,
        private ?array $tags = [],
        // Full EU VAT-ID regex, copied verbatim from Dotypos API docs (validation#vatid) —
        // the server accepts any EU country, not just CZ/SK/PL, despite Dotypos being a Czech product.
        #[Assert\Regex(
            '/^((AT)U[0-9]{8}|(BE)0[0-9]{9}|(BG)[0-9]{9,10}|(CY)[0-9]{8}L|(CZ)[0-9]{8,10}|(DE)[0-9]{9}|(DK)[0-9]{8}'
            . '|(EE)[0-9]{9}|(EL|GR)[0-9]{9}|(ES)[0-9A-Z][0-9]{7}[0-9A-Z]|(FI)[0-9]{8}|(FR)[0-9A-Z]{2}[0-9]{9}'
            . '|(GB)([0-9]{9}([0-9]{3})|[A-Z]{2}[0-9]{3})|(HU)[0-9]{8}|(IE)[0-9]S[0-9]{5}L|(IT)[0-9]{11}'
            . '|(LT)([0-9]{9}|[0-9]{12})|(LU)[0-9]{8}|(LV)[0-9]{11}|(MT)[0-9]{8}|(NL)[0-9]{9}B[0-9]{2}|(PL)[0-9]{10}'
            . '|(PT)[0-9]{9}|(RO)[0-9]{2,10}|(SE)[0-9]{12}|(SI)[0-9]{8}|(SK)[0-9]{10})$/'
        )]
        private ?string $vatId = '',
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