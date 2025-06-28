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
                $q->whereRaw("STR_TO_DATE(start_time, '%h:%i %p') < STR_TO_DATE(?, '%H:%i')", [$end_time])
                    ->whereRaw("STR_TO_DATE(end_time, '%h:%i %p') > STR_TO_DATE(?, '%H:%i')", [$start_time]);
            })
            ->exists();
    }
}
