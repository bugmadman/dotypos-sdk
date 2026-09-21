<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Reservation\ValueObject;

use BMM\DotyposSdk\Infrastructure\DataTransformer\PartialUpdatePayload;
use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use BMM\DotyposSdk\Reservation\ReservationStatus;

/**
 * Partial update payload for `PATCH .../reservations/:id` — every field defaults to
 * `null`, and `null` means "leave unchanged," not "clear this field." Only fields
 * explicitly set are sent to the API; see {@see PartialUpdatePayload}.
 */
final readonly class ReservationPatchVO extends ValueObject implements PartialUpdatePayload
{
    public function __construct(
        private ?int $_branchId = null,
        private ?int $_customerId = null,
        private ?int $_employeeId = null,
        private ?int $_tableId = null,
        private ?\DateTimeImmutable $endDate = null,
        private ?int $flags = null,
        private ?ReservationStatus $status = null,
        private ?int $seats = null,
        private ?\DateTimeImmutable $startDate = null,
        private ?string $note = null,
    ) {
        $this->validate();
    }

    public function getBranchId(): ?int
    {
        return $this->_branchId;
    }

    public function getCustomerId(): ?int
    {
        return $this->_customerId;
    }

    public function getEmployeeId(): ?int
    {
        return $this->_employeeId;
    }

    public function getTableId(): ?int
    {
        return $this->_tableId;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getFlags(): ?int
    {
        return $this->flags;
    }

    public function getStatus(): ?ReservationStatus
    {
        return $this->status;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
}
