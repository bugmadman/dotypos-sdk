<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Branch\DTO;

use BMM\DotyposSdk\Infrastructure\DTO\DTO;

final class BranchDTO extends DTO
{
    public int $_cloudId;
    public string $created;
    public bool $deleted;
    public bool $display;
    public int $features;
    public int $flags;
    public int $id;
    public string $name;
    public string $versionDate;
}
