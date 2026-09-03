<?php

namespace BMM\DotyposSdk\DiscountGroup\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Infrastructure\Trait\PaginationTraitDTO;

final class DiscountGroupsDTO extends DTO
{
    use PaginationTraitDTO;

    /** @var DiscountGroupDTO[] */
    public array $data;
}