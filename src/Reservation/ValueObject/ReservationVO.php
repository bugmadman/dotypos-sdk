<?php

namespace BMM\DotyposSdk\Reservation\ValueObject;

use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use BMM\DotyposSdk\Reservation\ReservationStatus;

final class ReservationVO extends ValueObject
{
    private \DateTime $dateTime;

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function __construct(
        private readonly int $_branchId,
        private readonly int $_customerId,
        private readonly int $_employeeId,
        private readonly int $_tableId,
        private readonly \DateTimeImmutable $endDate,
        private readonly int $flags,
        private readonly ReservationStatus $status,
        private readonly int $seats,
        private readonly \DateTimeImmutable $startDate,
        private readonly ?int $id = null,
        private readonly ?string $note = null,
    ) {
        $this->validate($this);
    }

    public function getBranchId(): int
    {
        return $this->_branchId;
    }

    public function getCustomerId(): int
    {
        return $this->_customerId;
    }

    public function getEmployeeId(): int
    {
        return $this->_employeeId;
    }

    public function getTableId(): int
    {
        return $this->_tableId;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function getStatus(): ReservationStatus
    {
        return $this->status;
    }

    public function getSeats(): int
    {
        return $this->seats;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
}
