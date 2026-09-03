<?php

namespace BMM\DotyposSdk\Order\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Infrastructure\Trait\PaginationTraitDTO;

final class OrdersDTO extends DTO
{
    use PaginationTraitDTO;

    /** @var OrderDTO[] */
    public array $data;
}