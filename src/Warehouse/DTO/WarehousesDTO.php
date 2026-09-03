<?php

namespace BMM\DotyposSdk\Warehouse\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Infrastructure\Trait\PaginationTraitDTO;

final class WarehousesDTO extends DTO
{
    use PaginationTraitDTO;

    /** @var WarehouseDTO[] */
    public array $data;
}
