<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Tests\OrderItem;

use BMM\DotyposSdk\Api;
use BMM\DotyposSdk\OrderItem\DTO\OrderItemCustomizationDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class OrderItemTraitTest extends TestCase
{
    /**
     * Regression test for plan finding 5.1 (bare array) plus the negative sentinel
     * ID values (`_categoryId`, `_employeeId`, `_productId`) and the nested
     * `orderItemCustomizations` object shape.
     */
    public function testGetOrderItemsDeserializesBareArrayResponse(): void
    {
        $fixture = (string) file_get_contents(__DIR__ . '/../Fixtures/orderItems.json');
        $httpClient = new MockHttpClient(new MockResponse($fixture, ['response_headers' => ['ETag' => 'etag-1']]));
        $api = new Api(cloudId: 1, accessToken: 'token', httpClient: $httpClient);

        $items = $api->getOrderItems();

        self::assertCount(1, $items);

        $item = $items[0];
        self::assertSame(200, $item->id);
        self::assertSame(-201, $item->_categoryId);
        self::assertSame(-1, $item->_employeeId);
        self::assertSame(-401, $item->_productId);
        self::assertNull($item->_customerId);
        self::assertSame(12.5, $item->discountPercent);
        self::assertSame(['1234567890123'], $item->ean);
        self::assertSame('etag-1', $item->eTag);

        self::assertCount(1, $item->orderItemCustomizations);
        self::assertInstanceOf(OrderItemCustomizationDTO::class, $item->orderItemCustomizations[0]);
        self::assertSame('Extra shot', $item->orderItemCustomizations[0]->name);
    }

    public function testGetOrderItemDeserializesSingleResponse(): void
    {
        $fixtures = json_decode(
            (string) file_get_contents(__DIR__ . '/../Fixtures/orderItems.json'),
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

        $item = $api->getOrderItem(200);

        self::assertSame(200, $item->id);
        self::assertSame('etag-2', $item->eTag);
    }
}
