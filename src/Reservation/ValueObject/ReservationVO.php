<?php

namespace BMM\DotyposSdk\Reservation\ValueObject;

use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;

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
        private int $_branchId,
        private int $_customerId,
        private int $_employeeId,
        private int $_tableId,
        private \DateTimeImmutable $endDate,
        private int $flags,
        private string $status,
        private int $seats,
        private \DateTimeImmutable $startDate,
        private ?int $id = null,
        private ?string $note = null,
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

    public function getStatus(): string
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
