<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Table\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;
use BMM\DotyposSdk\Table\TableType;

final class TableDTO extends DTO
{
    public string $_branchId;
    public string $_cloudId;
    public ?int $_sellerId = null;
    public int $_tableGroupId;
    public bool $display;
    public bool $enabled;
    public int $id;
    public string $locationName;
    public string $name;
    public float $positionX;
    public float $positionY;
    public int $rotation;
    public int $seats;
    /** @var string[] */
    public array $tags;
    public TableType $type;
    public string $versionDate;
}
