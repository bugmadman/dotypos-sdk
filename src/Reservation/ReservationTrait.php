<?php

namespace BMM\DotyposSdk\Reservation;

use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\PaginationVO;
use BMM\DotyposSdk\Infrastructure\HttpClient\ValueObject\RequestVO;
use BMM\DotyposSdk\Reservation\DTO\ReservationDTO;
use BMM\DotyposSdk\Reservation\DTO\ReservationsDTO;
use BMM\DotyposSdk\Reservation\ValueObject\ReservationVO;

trait ReservationTrait
{
    public function getReservation(int $id): ReservationDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getReservation()->getUrl(),
            path: $this->getEndpoint()->getReservation()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->getReservation()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $reservation = $this->deserialize($response->data, ReservationDTO::class, $response->etag);

        return $reservation;
    }

    public function getReservations(?PaginationVO $pagination): ReservationsDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->getReservations()->getUrl(),
            path: $this->getEndpoint()->getReservations()->getPath(),
            requestsMethod: $this->getEndpoint()->getReservations()->getRequestsMethod(),
            pagination: $pagination
        );
        $response = $this->getHttpClient()->sendRequest($request);
        $reservations = $this->deserialize($response->data, ReservationsDTO::class, $response->etag);

//        TODO add support for page, limit, filter, sort
        return $reservations;
    }

    public function createReservation(ReservationVO $payload): ReservationDTO
    {
        $reservations = $this->createReservations([$payload]);

        return $reservations[0];
    }

    /**
     * @param ReservationVO[] $payload
     * @return ReservationDTO[]
     * @throws \Exception
     */
    public function createReservations(array $payload): array
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->createReservations()->getUrl(),
            path: $this->getEndpoint()->createReservations()->getPath(),
            requestsMethod: $this->getEndpoint()->createReservations()->getRequestsMethod(),
            data: $this->serialize($payload)
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserializeMany($response->data, ReservationDTO::class);
    }

    public function replaceReservation(ReservationVO $payload, string $eTag): ReservationDTO
    {
        $id = $payload->getId();
        if ($id === null) {
            throw new \Exception('The id field is required.');
        }

        $request = new RequestVO(
            uri: $this->getEndpoint()->replaceReservation()->getUrl(),
            path: $this->getEndpoint()->replaceReservation()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->replaceReservation()->getRequestsMethod(),
            data: $this->serialize($payload),
            eTag: $eTag
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, ReservationDTO::class);
    }

    /**
     * @param ReservationVO[] $payload
     * @return ReservationDTO[]
     */
    public function replaceReservations(array $payload, string $eTag): array
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->replaceReservations()->getUrl(),
            path: $this->getEndpoint()->replaceReservations()->getPath(),
            requestsMethod: $this->getEndpoint()->replaceReservations()->getRequestsMethod(),
            data: $this->serialize($payload),
            eTag: $eTag
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserializeMany($response->data, ReservationDTO::class);
    }

    public function deleteReservation(int $id): ReservationDTO
    {
        $request = new RequestVO(
            uri: $this->getEndpoint()->deleteReservation()->getUrl(),
            path: $this->getEndpoint()->deleteReservation()->getPath() . '/' . $id,
            requestsMethod: $this->getEndpoint()->deleteReservation()->getRequestsMethod(),
        );
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->deserialize($response->data, ReservationDTO::class);
    }
}
