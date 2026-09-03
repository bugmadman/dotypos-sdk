<?php

namespace BMM\DotyposSdk\Reservation\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Infrastructure\Trait\PaginationTraitDTO;

final class ReservationsDTO extends DTO
{
    use PaginationTraitDTO;

    /** @var ReservationDTO[] */
    public array $data;
}
