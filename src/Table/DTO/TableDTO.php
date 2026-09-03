<?php

namespace BMM\DotyposSdk\Table\DTO;

final class TableDTO
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
    public array $tags;
//    From enum SQUARE, SQUARE6, CIRCLE2, CIRCLE4, DELIVERY, CHAIR_SINGLE, ROUND, DOOR, GENERIC, CAR1, CAR2
    public string $type;
    public string $versionDate;
}
