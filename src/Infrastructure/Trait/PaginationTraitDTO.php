<?php

namespace BMM\DotyposSdk\Infrastructure\Trait;

trait PaginationTraitDTO
{
    public int $currentPage;
    public int $perPage;
    public int $totalItemsOnPage;

    public ?int $totalItemsCount = null;
    public int $firstPage;
    public ?int $lastPage = null;

    public ?int $nextPage = null;
    public ?int $prevPage = null;
}
