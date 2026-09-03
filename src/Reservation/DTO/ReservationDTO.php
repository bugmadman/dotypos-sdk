<?php

namespace BMM\DotyposSdk\Reservation\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;

final class ReservationDTO extends DTO
{
    public string $_branchId;
    public string $_cloudId;
    public int $_customerId;
    public int $_employeeId;
    public int $_tableId;
    public string $created;
    public string $endDate;
    public int $flags;
    public int $id;
    public ?string $note = null;
    public int $seats;
    public string $startDate;
    public string $status;
    public string $versionDate;
}
