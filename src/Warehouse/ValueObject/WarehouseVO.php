<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Warehouse\ValueObject;

use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class WarehouseVO extends ValueObject
{
    public function __construct(
        private string $name,
        private ?bool $enabled = true,
        private ?string $barcode = null,
        #[Assert\Regex('/^#[0-9A-Fa-f]{6}$/')]
        private ?string $hexColor = null,
        private ?int $id = null,
    ) {
        $this->validate();
    }

    public function getName(): string
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

    public function getId(): ?int
    {
        return $this->id;
    }
}
