<?php

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

use BMM\DotyposSdk\Infrastructure\ValueObject\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class PaginationVO extends ValueObject
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        private ?int $page = 1,
        #[Assert\Range(min: 1, max: 100)]
        private ?int $limit = 20
    ) {
        $this->validate($this);
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }
}