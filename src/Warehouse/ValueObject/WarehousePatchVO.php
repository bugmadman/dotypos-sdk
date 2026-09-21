<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Warehouse\ValueObject;

use BMM\DotyposSdk\Infrastructure\DataTransformer\PartialUpdatePayload;
use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Partial update payload for `PATCH .../warehouses/:id` — every field defaults to
 * `null`, and `null` means "leave unchanged," not "clear this field." Only fields
 * explicitly set are sent to the API; see {@see PartialUpdatePayload}.
 */
final readonly class WarehousePatchVO extends ValueObject implements PartialUpdatePayload
{
    public function __construct(
        private ?string $name = null,
        private ?bool $enabled = null,
        private ?string $barcode = null,
        #[Assert\Regex('/^#[0-9A-Fa-f]{6}$/')]
        private ?string $hexColor = null,
    ) {
        $this->validate();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function getHexColor(): ?string
    {
        return $this->hexColor;
    }
}
