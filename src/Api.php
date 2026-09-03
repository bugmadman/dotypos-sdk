<?php

namespace BMM\DotyposSdk;

use BMM\DotyposSdk\Branch\BranchTrait;
use BMM\DotyposSdk\Customer\CustomerTrait;
use BMM\DotyposSdk\DiscountGroup\DiscountGroupTrait;
use BMM\DotyposSdk\Infrastructure\DataTransformer\DeserializerTrait;
use BMM\DotyposSdk\Infrastructure\DataTransformer\SerializerTrait;
use BMM\DotyposSdk\Infrastructure\HttpClient\HttpClient;
use BMM\DotyposSdk\Order\OrderTrait;
use BMM\DotyposSdk\OrderItem\OrderItemTrait;
use BMM\DotyposSdk\Reservation\ReservationTrait;
use BMM\DotyposSdk\Table\TableTrait;
use BMM\DotyposSdk\Warehouse\WarehouseTrait;
use BMM\DotyposSdk\Webhook\WebhookTrait;

final class Api
{
    use DeserializerTrait;
    use SerializerTrait;
    use CustomerTrait;
    use DiscountGroupTrait;
    use OrderTrait;
    use OrderItemTrait;
    use ReservationTrait;
    use TableTrait;
    use BranchTrait;
    use WebhookTrait;
    use WarehouseTrait;

    public function __construct(
        private int $cloudId,
        private string $accessToken,
    ) {
        //TODO think about it
        $_SESSION['cloudId'] = $cloudId;
        $_SESSION['accessToken'] = $accessToken;
    }

    private function getEndpoint(): Endpoint
    {
        return new Endpoint();
    }

    private function getHttpClient(): HttpClient
    {
        return new HttpClient();
    }
}
