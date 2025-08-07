<?php

namespace App\Services;

use App\Models\Booking;

class BookingServices
{
    public static function Book($UserId, $venueId, $bookingDate, $start_time, $end_time)
    {
        return Booking::create([
            'user_id' => $UserId,
            'venue_id' => $venueId,
            'booking_date' => $bookingDate,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);
    }
}
