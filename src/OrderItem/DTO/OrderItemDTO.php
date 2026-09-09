<?php

namespace BMM\DotyposSdk\OrderItem\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;

final class OrderItemDTO extends DTO
{
    public int $_branchId;
    // TODO check _categoryId":"-201"
    public int $_categoryId;
    public int $_cloudId;
    public ?int $_courseId = null;
    // TODO check _customerId":"-1"
    public int $_customerId;
    public ?int $_eetSubjectId = null;
    //TODO check _employeeId":"-1"
    public int $_employeeId;
    public int $_orderId;
    //TODO check "_productId":"-401"
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
    public int $discountPercent;
    public bool $discountPermitted;
    public ?string $ean = null;
    public int $flags;
    public int $id;
    public string $name;
    public ?string $note = null;
    public bool $onSale;
    public int $packaging;
    public bool $parked;
    public int $points;
    public int $quantity;
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
    public int $vat;
    public string $versionDate;
    // TODO real element shape unverified — check against API docs (see plan item 5.1)
    /** @var array<int, mixed> */
    public array $orderItemCustomizations;
}
