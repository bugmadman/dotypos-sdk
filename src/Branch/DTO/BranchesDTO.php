<?php

namespace BMM\DotyposSdk\Branch\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Infrastructure\Trait\PaginationTraitDTO;

final class BranchesDTO extends DTO
{
    use PaginationTraitDTO;

    /** @var BranchDTO[] */
    public array $data;
}
