<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Customer\ValueObject;

use BMM\DotyposSdk\Infrastructure\DataTransformer\PartialUpdatePayload;
use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Partial update payload for `PATCH .../customers/:id` — every field defaults to
 * `null`, and `null` means "leave unchanged," not "clear this field." Only fields
 * explicitly set are sent to the API; see {@see PartialUpdatePayload}.
 */
final readonly class CustomerPatchVO extends ValueObject implements PartialUpdatePayload
{
    /**
     * @param string[]|null $tags
     */
    public function __construct(
        private ?int $_discountGroupId = null,
        private ?int $_sellerId = null,
        private ?string $addressLine1 = null,
        private ?string $addressLine2 = null,
        private ?string $barcode = null,
        private ?string $birthday = null,
        private ?string $city = null,
        private ?string $companyId = null,
        private ?string $companyId2 = null,
        private ?string $companyName = null,
        private ?string $country = null,
        private ?bool $display = null,
        private ?string $email = null,
        private ?string $expireDate = null,
        private ?string $externalId = null,
        private ?string $firstName = null,
        private ?string $headerPrint = null,
        #[Assert\Regex('/^#[0-9A-Fa-f]{6}$/')]
        private ?string $hexColor = null,
        private ?string $internalNote = null,
        private ?string $lastName = null,
        private ?string $note = null,
        private ?string $phone = null,
        private ?float $points = null,
        private ?array $tags = null,
        #[Assert\Regex(
            '/^((AT)U[0-9]{8}|(BE)0[0-9]{9}|(BG)[0-9]{9,10}|(CY)[0-9]{8}L|(CZ)[0-9]{8,10}|(DE)[0-9]{9}|(DK)[0-9]{8}'
            . '|(EE)[0-9]{9}|(EL|GR)[0-9]{9}|(ES)[0-9A-Z][0-9]{7}[0-9A-Z]|(FI)[0-9]{8}|(FR)[0-9A-Z]{2}[0-9]{9}'
            . '|(GB)([0-9]{9}([0-9]{3})|[A-Z]{2}[0-9]{3})|(HU)[0-9]{8}|(IE)[0-9]S[0-9]{5}L|(IT)[0-9]{11}'
            . '|(LT)([0-9]{9}|[0-9]{12})|(LU)[0-9]{8}|(LV)[0-9]{11}|(MT)[0-9]{8}|(NL)[0-9]{9}B[0-9]{2}|(PL)[0-9]{10}'
            . '|(PT)[0-9]{9}|(RO)[0-9]{2,10}|(SE)[0-9]{12}|(SI)[0-9]{8}|(SK)[0-9]{10})$/'
        )]
        private ?string $vatId = null,
        private ?string $zip = null,
        private ?int $flags = null,
        private ?bool $deleted = null,
    ) {
        $this->validate();
    }

    public function getDiscountGroupId(): ?int
    {
        return $this->_discountGroupId;
    }

    public function getSellerId(): ?int
    {
        return $this->_sellerId;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCompanyId(): ?string
    {
        return $this->companyId;
    }

    public function getCompanyId2(): ?string
    {
        return $this->companyId2;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getDisplay(): ?bool
    {
        return $this->display;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getExpireDate(): ?string
    {
        return $this->expireDate;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    /**
     * @return string[]|null
     */
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

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }
}
