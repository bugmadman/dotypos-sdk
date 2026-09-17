<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Tests\Order;

use BMM\DotyposSdk\Api;
use BMM\DotyposSdk\Order\OrderStatus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class OrderTraitTest extends TestCase
{
    /**
     * Regression test for plan finding 5.1: the API returns a bare JSON array for
     * `GET .../orders`, not a wrapper object with pagination.
     */
    public function testGetOrdersDeserializesBareArrayResponse(): void
    {
        $fixture = (string) file_get_contents(__DIR__ . '/../Fixtures/orders.json');
        $httpClient = new MockHttpClient(new MockResponse($fixture, ['response_headers' => ['ETag' => 'etag-1']]));
        $api = new Api(cloudId: 1, accessToken: 'token', httpClient: $httpClient);

        $orders = $api->getOrders();

        self::assertCount(1, $orders);

        $order = $orders[0];
        self::assertSame(100, $order->id);
        self::assertSame(OrderStatus::Closed, $order->status);
        self::assertSame(2, $order->guestCount);
        self::assertSame(0.5, $order->points);
        self::assertSame('etag-1', $order->eTag);
    }

    /**
     * The documented Order status list is explicitly non-exhaustive — a status value
     * outside the documented set must deserialize to OrderStatus::Unknown rather than
     * throwing.
     */
    public function testUnknownOrderStatusFallsBackToUnknownCase(): void
    {
        $fixtures = json_decode(
            (string) file_get_contents(__DIR__ . '/../Fixtures/orders.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertIsArray($fixtures);
        self::assertIsArray($fixtures[0]);

        $fixtures[0]['status'] = 'some_future_status_not_in_docs';
        $httpClient = new MockHttpClient(new MockResponse((string) json_encode($fixtures)));
        $api = new Api(cloudId: 1, accessToken: 'token', httpClient: $httpClient);

        $orders = $api->getOrders();

        self::assertSame(OrderStatus::Unknown, $orders[0]->status);
    }

    public function testGetOrderDeserializesSingleResponse(): void
    {
        $fixtures = json_decode(
            (string) file_get_contents(__DIR__ . '/../Fixtures/orders.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertIsArray($fixtures);
        self::assertIsArray($fixtures[0]);

        $httpClient = new MockHttpClient(
            new MockResponse((string) json_encode($fixtures[0]), ['response_headers' => ['ETag' => 'etag-2']])
        );
        $api = new Api(cloudId: 1, accessToken: 'token', httpClient: $httpClient);

        $order = $api->getOrder(100);

        self::assertSame(100, $order->id);
        self::assertSame('etag-2', $order->eTag);
    }
}
