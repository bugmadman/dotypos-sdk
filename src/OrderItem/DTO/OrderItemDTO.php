<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\OrderItem\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;

final class OrderItemDTO extends DTO
{
    public int $_branchId;
    public int $_categoryId;
    public int $_cloudId;
    public ?int $_courseId = null;
    public ?int $_customerId = null;
    public ?int $_eetSubjectId = null;
    public int $_employeeId;
    public int $_orderId;
    public int $_productId;
    public ?int $_relatedOrderItemId = null;
    public ?int $_sellerId = null;
    public ?string $alternativeName = null;
    public float $billedUnitPriceWithVat;
    public float $billedUnitPriceWithoutVat;
    public ?string $canceledDate = null;
    public string $completed;
    public string $created;
    public string $currency;
    public float $discountPercent;
    public bool $discountPermitted;
    /** @var string[]|null */
    public ?array $ean = null;
    public int $flags;
    public int $id;
    public string $name;
    public ?string $note = null;
    public bool $onSale;
    public float $packaging;
    public bool $parked;
    public float $points;
    public float $quantity;
    public ?int $preparationDuration = null;
    public bool $stockDeduct;
    public ?string $subtitle = null;
    /** @var string[] */
    public array $tags;
    public float $totalPriceWithVat;
    public float $totalPriceWithoutVat;
    public string $unit;
    public float $unitPriceWithVat;
    public float $unitPriceWithoutVat;
    public ?float $unitPurchasePrice = null;
    public string $updated;
    public float $vat;
    public string $versionDate;
    /** @var OrderItemCustomizationDTO[] */
    public array $orderItemCustomizations;
}
