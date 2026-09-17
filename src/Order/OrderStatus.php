<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Order;

use BMM\DotyposSdk\Infrastructure\DataTransformer\TolerantBackedEnum;

/**
 * Per the documentation (api-reference/enums/order-status/), this list is not
 * exhaustive — the API reserves an undocumented fallback value. {@see self::Unknown}
 * is that fallback, not a real API value, so future/undocumented statuses deserialize
 * into it instead of failing.
 */
enum OrderStatus: string implements TolerantBackedEnum
{
    case New = 'new';
    case Parked = 'parked';
    case ReadyToPickup = 'ready_to_pickup';
    case ReadyForDelivery = 'ready_for_delivery';
    case OnDelivery = 'on_delivery';
    case Delivered = 'delivered';
    case DeliveryFailed = 'delivery_failed';
    case Canceled = 'canceled';
    case Closed = 'closed';
    case Unknown = 'unknown';

    public static function fromApiValue(string $value): self
    {
        return self::tryFrom($value) ?? self::Unknown;
    }
}
