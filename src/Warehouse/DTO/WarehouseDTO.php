<?php

namespace BMM\DotyposSdk\Warehouse\DTO;

final class WarehouseDTO
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
