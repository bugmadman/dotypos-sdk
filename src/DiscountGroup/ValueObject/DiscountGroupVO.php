<?php

namespace BMM\DotyposSdk\DiscountGroup\ValueObject;

use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;

final readonly class DiscountGroupVO extends ValueObject
{
    public function __construct(
        public string $name,
        public ?bool $display = true,
        public ?int $discountPercent = 0,
        public ?bool $deleted = false,
        public ?int $id = null,
        public ?string $externalId = null,
    ) {
        $this->validate();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplay(): ?bool
    {
        return $this->display;
    }

    public function getDiscountPercent(): ?int
    {
        return $this->discountPercent;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }
}
