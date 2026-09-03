<?php

namespace BMM\DotyposSdk\Table\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Infrastructure\Trait\PaginationTraitDTO;

final class TablesDTO extends DTO
{
    use PaginationTraitDTO;

    /** @var TableDTO[] */
    public array $data;
}
