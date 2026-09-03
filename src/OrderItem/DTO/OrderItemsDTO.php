<?php

namespace BMM\DotyposSdk\OrderItem\DTO;


use BMM\DotyposSdk\Infrastructure\Trait\PaginationTraitDTO;

final class OrderItemsDTO
{
    use PaginationTraitDTO;

    /** @var OrderItemDTO[] */
    public array $data;
}