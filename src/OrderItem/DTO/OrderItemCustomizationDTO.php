<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\OrderItem\DTO;

final class OrderItemCustomizationDTO
{
    public int $_branchId;
    public int $_cloudId;
    public int $_orderId;
    public int $_orderItemId;
    public int $_productCustomizationId;
    public int $_productId;
    public ?string $alternativeName = null;
    public ?string $canceledDate = null;
    public string $created;
    public string $defaultSelection;
    public float $discountValue;
    public int $flags;
    public int $id;
    public string $name;
    public ?int $preparationDuration = null;
    public float $purchasePriceWithoutVat;
    public float $quantity;
    public string $unit;
    public float $unitPriceWithVat;
    public float $vat;
    public string $versionDate;
}
