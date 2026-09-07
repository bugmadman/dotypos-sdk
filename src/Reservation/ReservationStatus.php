<?php

namespace BMM\DotyposSdk\Reservation;

enum ReservationStatus: string
{
    case New = 'NEW';
    case Cancelled = 'CANCELLED';
    case Confirmed = 'CONFIRMED';
}
