<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject;

enum FilterOperation: string
{
    case Equals = 'eq';
    case NotEquals = 'ne';
    case GreaterThan = 'gt';
    case GreaterThanOrEqual = 'gteq';
    case LessThan = 'lt';
    case LessThanOrEqual = 'lteq';
    case Like = 'like';
    case In = 'in';
    case NotIn = 'notin';
    case BitsSet = 'bin';
    case BitsNotSet = 'bex';
}
