<?php

namespace BMM\DotyposSdk\DiscountGroup\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;

final class DiscountGroupDTO extends DTO
{
    public int $_cloudId;
    public bool $deleted;
    public int $discountPercent;
    public bool $display;
    public ?string $externalId = null;
    public int $id;
    public string $name;

    public string $versionDate;
}
