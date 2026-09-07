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
    public ?int $_courseId;
    public int $_customerId;
    public ?int $_eetSubjectId;
    public int $_employeeId;
    public ?int $_relatedInvoiceId;
    public ?int $_relatedOrderId;
    public ?int $_sellerId;
    public ?int $_sourceOrderId;
    public ?int $_tableId;
    public ?string $bkp;
    public ?string $canceledDate;
    public string $completed;
    public string $created;
    public string $currency;
    public string $documentNumber;
    public string $documentType;
    public ?string $externalId;
    public ?string $fik;
    public int $flags;
    public int $id;
    public int $itemCount;
    public float $locationAccuracy;
    public string $locationDate;
    public float $locationLatitude;
    public float $locationLongitude;
    public ?string $merchantPrintData;
    public ?string $note;
    public bool $paid;
    public bool $parked;
    public ?string $pkp;
    public int $points;
    public ?string $printData;
    public string $status;
    public array $tags;
    public float $tipAmount;
    public float $totalValueRounded;
    public string $updated;
    public string $versionDate;
}
