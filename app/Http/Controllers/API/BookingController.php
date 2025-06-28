<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\BookingHelper;
use App\Utils\ErrorResponse;
use App\Utils\SuccessResponse;

class BookingController extends Controller
{
    public function reserve(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'venue_id' => 'required|exists:venues,id',
            'booking_date' => 'required|date|after_or_equal:today|before_or_equal:' . now()->addMonth()->toDateString(),
            'start_time' => 'required|date_format:h:i A',
            'end_time' => 'required|date_format:h:i A|after:start_time',
        ]);

        try {
            $UserId = $request->user_id;
            $venueId = $request->venue_id;
            $bookingDate = $request->booking_date;

            $startTime = Carbon::createFromFormat('h:i A', $request->start_time)->format('H:i');
            $endTime = Carbon::createFromFormat('h:i A', $request->end_time)->format('H:i');


            $venue = Venue::find($venueId);

            [$open, $close] = explode(' - ', $venue->working_hours);

            $openTime = Carbon::createFromFormat('h:i A', $open)->format('H:i');
            $closeTime = Carbon::createFromFormat('h:i A', $close)->format('H:i');


            if ($startTime < $openTime || $endTime > $closeTime) {
                return ErrorResponse::error('Booking time must be within venue working hours: ' . $venue->working_hours, 422);
            }

            $overlap = BookingHelper::CheckOverlap($venueId, $bookingDate, $startTime, $endTime);

            if ($overlap) {
                return ErrorResponse::error('Time slot overlaps with existing booking', 409);
            }

            $booking = Booking::create([
                'user_id' => $UserId,
                'venue_id' => $venueId,
                'booking_date' => $bookingDate,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);

            return SuccessResponse::success('Booking successful', $booking, 201);
        } catch (\Exception $e) {
            return ErrorResponse::error($e->getMessage());
        }
    }


    public function list_venues()
    {
        try {
            $venueData = Booking::select('venue_id')
                ->selectRaw('COUNT(*) as booking_count')
                ->groupBy('venue_id')
                ->get();

            if ($venueData->isEmpty()) {
                return ErrorResponse::error('No venues list found', 404);
            }

            $maxCount = $venueData->max('booking_count');
            $minCount = $venueData->min('booking_count');

            $venueDetails = [];

            foreach ($venueData as $data) {
                $venue = Venue::find($data->venue_id);

                if ($venue) {
                    $highlight = null;
                    if ($data->booking_count == $maxCount) {
                        $highlight = 'highest';
                    } elseif ($data->booking_count == $minCount) {
                        $highlight = 'lowest';
                    }

                    $venueDetails[] = [
                        'venue_id' => $venue->id,
                        'venue_name' => $venue->venue_name,
                        'booking_count' => $data->booking_count,
                        'highlight' => $highlight
                    ];
                }
            }

            return SuccessResponse::Success('Venue details fetched successfully', $venueDetails);
        } catch (\Exception $e) {
            return ErrorResponse::error($e->getMessage());
        }
    }


    public function categorize_venues()
    {
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;

            $bookingCounts = Booking::whereYear('booking_date', $currentYear)
                ->whereMonth('booking_date', $currentMonth)
                ->groupBy('venue_id')
                ->get([
                    'venue_id',
                    DB::raw('COUNT(*) as booking_count')
                ]);

            if ($bookingCounts->isEmpty()) {
                return ErrorResponse::error('No venues found as per the category', 404);
            }


            $venues = Venue::whereIn('id', $bookingCounts->pluck('venue_id'))->get()->keyBy('id');

            $venueDetails = [];

            foreach ($bookingCounts as $bc) {
                $category = 'D';

                if ($bc->booking_count > 15) {
                    $category = 'A';
                } elseif ($bc->booking_count >= 10) {
                    $category = 'B';
                } elseif ($bc->booking_count >= 5) {
                    $category = 'C';
                }

                if (isset($venues[$bc->venue_id])) {
                    $venueDetails[] = [
                        'venue_id' => $bc->venue_id,
                        'venue_name' => $venues[$bc->venue_id]->venue_name,
                        'booking_count' => $bc->booking_count,
                        'category' => $category
                    ];
                }
            }
            return SuccessResponse::success('Venue performance categorized successfully', $venueDetails);
        } catch (\Exception $e) {
            return ErrorResponse::error($e->getMessage());
        }
    }
}
