<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Warehouse\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;

final class WarehouseDTO extends DTO
{
    public int $_cloudId;
    public ?string $barcode = null;
    public bool $deleted;
    public bool $enabled;
    public string $hexColor;
    public int $id;
    public string $name;
    public string $versionDate;
}
