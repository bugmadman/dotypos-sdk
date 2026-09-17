<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Order\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Order\OrderStatus;

final class OrderDTO extends DTO
{
    public string $_branchId;
    public string $_cloudId;
    public ?int $_courseId = null;
    public int $_customerId;
    public ?int $_eetSubjectId = null;
    public int $_employeeId;
    public ?int $_relatedInvoiceId = null;
    public ?int $_relatedOrderId = null;
    public ?int $_sellerId = null;
    public ?int $_sourceOrderId = null;
    public ?int $_tableId = null;
    public ?string $bkp = null;
    // TODO $canceledDate/$completed/$created/$locationDate/$updated/$versionDate below:
    // date to DateTimeImmutable — см. docs/IMPLEMENTATION_PLAN.md, "После релиза"
    public ?string $canceledDate = null;
    public string $completed;
    public string $created;
    public string $currency;
    public string $documentNumber;
    public string $documentType;
    public ?string $externalId = null;
    public ?string $fik = null;
    public int $flags;
    public int $guestCount;
    public int $id;
    public int $itemCount;
    public float $locationAccuracy;
    public string $locationDate;
    public float $locationLatitude;
    public float $locationLongitude;
    public ?string $merchantPrintData = null;
    public ?string $note = null;
    public bool $paid;
    public bool $parked;
    public ?string $pkp = null;
    public float $points;
    public ?string $printData = null;
    public OrderStatus $status;
    /** @var string[] */
    public array $tags;
    public float $tipAmount;
    public float $totalValueRounded;
    public string $updated;
    public string $versionDate;
}
