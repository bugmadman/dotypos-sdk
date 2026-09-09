<?php

namespace BMM\DotyposSdk\Order\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;

final class OrderDTO extends DTO
{
//    public int $_cloudId;
//    public bool $deleted;
//    public int $discountPercent;
//    public bool $display;
//    public ?string $externalId;
//    public int $id;
//    public string $name;
//
////    TODO date to DateTimeImmutable
//    public string $versionDate;

//todo check it
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
    public ?string $canceledDate = null;
    public string $completed;
    public string $created;
    public string $currency;
    public string $documentNumber;
    public string $documentType;
    public ?string $externalId = null;
    public ?string $fik = null;
    public int $flags;
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
    public int $points;
    public ?string $printData = null;
    public string $status;
    /** @var string[] */
    public array $tags;
    public float $tipAmount;
    public float $totalValueRounded;
    public string $updated;
    public string $versionDate;
}
