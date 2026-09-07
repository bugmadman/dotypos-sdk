<?php

namespace BMM\DotyposSdk\Infrastructure\DTO;

class DTO
{
//    convert to trait
    public readonly ?string $eTag;

    public function setETag(?string $eTag): void
    {
        $this->eTag = $eTag;
    }
}