<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

enum SortDirection: string
{
    case Ascending = 'asc';
    case Descending = 'desc';
}
