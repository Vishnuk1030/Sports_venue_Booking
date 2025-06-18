<?php

namespace App\Helpers;

use App\Models\Booking;

class BookingHelper
{
    public static function CheckOverlap($venue_id, $booking_date, $start_time, $end_time)
    {
        return Booking::where('venue_id', $venue_id)
            ->where('booking_date', $booking_date)
            ->where(function ($q) use ($start_time, $end_time) {
                $q->where('start_time', '<', $end_time)
                    ->where('end_time', '>', $start_time);
            })
            ->exists();
    }
}
