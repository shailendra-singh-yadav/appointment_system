<?php

namespace App\Services;

use App\Models\Booking;

class BookingService
{
    public function getBookingsList($dataObject)
    {
        //dd($dataObject);
        $data = $this->bookingListDataQuery($dataObject);
        return  $data->paginate(10);
        
    }
    public function bookingListDataQuery($request)
    {
        $query = Booking::with(['user', 'guests'])
                        ->orderBy('created_at', 'desc');

        if ($request->has('bookingId')) {
            $query->where('id', $request->get('bookingId'));
        }

        return $query;
    }

}
