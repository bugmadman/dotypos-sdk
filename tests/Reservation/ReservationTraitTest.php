<?php

declare(strict_types=1);

namespace BMM\DotyposSdk\Tests\Reservation;

use BMM\DotyposSdk\Api;
use BMM\DotyposSdk\Reservation\ReservationStatus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class ReservationTraitTest extends TestCase
{
    /**
     * Regression test for plan finding 5.1: the API returns a bare JSON array for
     * `GET .../reservations`, not a wrapper object with pagination.
     */
    public function testGetReservationsDeserializesBareArrayResponse(): void
    {
        $fixture = (string) file_get_contents(__DIR__ . '/../Fixtures/reservations.json');
        $httpClient = new MockHttpClient(new MockResponse($fixture, ['response_headers' => ['ETag' => 'etag-1']]));
        $api = new Api(cloudId: 1, accessToken: 'token', httpClient: $httpClient);

        $reservations = $api->getReservations();

        self::assertCount(1, $reservations);

        $reservation = $reservations[0];
        self::assertSame(1, $reservation->id);
        self::assertSame(ReservationStatus::Confirmed, $reservation->status);
        self::assertSame(2, $reservation->seats);
        self::assertSame('etag-1', $reservation->eTag);
    }

    public function testGetReservationDeserializesSingleResponse(): void
    {
        $fixtures = json_decode(
            (string) file_get_contents(__DIR__ . '/../Fixtures/reservations.json'),
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

        $reservation = $api->getReservation(1);

        self::assertSame(1, $reservation->id);
        self::assertSame('etag-2', $reservation->eTag);
    }
}
