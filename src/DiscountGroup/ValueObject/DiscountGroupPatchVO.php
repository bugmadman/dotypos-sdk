<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\DiscountGroup\ValueObject;

use BMM\DotyposSdk\Infrastructure\DataTransformer\PartialUpdatePayload;
use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;

/**
 * Partial update payload for `PATCH .../discount-groups/:id` — every field defaults
 * to `null`, and `null` means "leave unchanged," not "clear this field." Only fields
 * explicitly set are sent to the API; see {@see PartialUpdatePayload}.
 */
final readonly class DiscountGroupPatchVO extends ValueObject implements PartialUpdatePayload
{
    public function __construct(
        private ?string $name = null,
        private ?bool $display = null,
        private ?float $discountPercent = null,
        private ?bool $deleted = null,
        private ?string $externalId = null,
    ) {
        $this->validate();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDisplay(): ?bool
    {
        return $this->display;
    }

    public function getDiscountPercent(): ?float
    {
        return $this->discountPercent;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
