<?php

namespace BMM\DotyposSdk\Customer\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Infrastructure\Trait\PaginationTraitDTO;

final class CustomersDTO extends DTO
{
    use PaginationTraitDTO;

    /** @var CustomerDTO[] */
    public array $data;
}