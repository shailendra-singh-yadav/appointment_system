<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Contracts\SendAppointmentNotificationInterface;
use App\Models\Guest;
use App\Models\BookingCancellation;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;


class BookingService
{
    public function getBookingsList($data)
    {
        $bookings = $this->bookingListDataQuery($data);
        return  $bookings->paginate(10);
        
    }
    public function bookingListDataQuery($data)
    {
        //dd($data['bookingId']);

        $query = Booking::with(['user', 'guests'])
                        ->orderBy('created_at', 'desc');

        if ($data->has('bookingId')) {
            //dd('1');
            $query->where('id', $data->get('bookingId'));
        }

        return $query;
    }

    public function createBooking($data, SendAppointmentNotificationInterface $notifier)
    {
        DB::beginTransaction();

        try {
            $appointment = Booking::create([
                'user_id' => Auth::id(),
                'title' => $data->title,
                'description' => $data->description,
                'booking_date' => $data->date,
                'reminder_preference' => $data->reminder_preference,
            ]);

            foreach ($data->guests as $email) {
                // Send email notification
                $notifier->send($appointment, $email, [
                    'name' => 'Guest User',
                    'join_link' => route('appointments.join', $appointment),
                ]);

                // Save guest
                Guest::create([
                    'booking_id' => $appointment->id,
                    'email' => $email,
                    'status' => '1',
                    'is_mail' => '1',
                ]);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => 'Appointment and guests saved successfully!',
            ];

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Appointment booking failed: ' . $e->getMessage());

            throw $e; // Let controller catch and handle response
        }
    }

    public function updateBooking($data, SendAppointmentNotificationInterface $notifier)
    {
        DB::beginTransaction();

        try {
            // Find the existing booking
            $appointment = Booking::findOrFail($data->appointment_id);

            // Update appointment details
            $appointment->update([
                'title' => $data->title,
                'description' => $data->description,
                'booking_date' => $data->date,
            ]);

            // Delete old guests
            Guest::where('booking_id', $appointment->id)->delete();

            // Add new guest emails and send notifications
            foreach ($data->guests as $email) {
                $notifier->send($appointment, $email, [
                    'name' => 'Guest User',
                    'join_link' => route('appointments.join', $appointment),
                ]);

                Guest::create([
                    'booking_id' => $appointment->id,
                    'email' => $email,
                    'status' => '1',
                    'is_mail' => '1',
                ]);
            }

            DB::commit();

            return [
                'status' => true,
                'message' => 'Appointment updated successfully!',
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Appointment update failed: ' . $e->getMessage());
            throw $e;
        }   
    }

   
    public function appointmentCancel($bookingId, $notifier): JsonResponse
    {
        
        DB::beginTransaction(); // Start DB transaction

        try {
            $booking = Booking::with('guests')->findOrFail($bookingId);

            // 1. Prevent duplicate cancellation
            if (BookingCancellation::where('booking_id', $bookingId)->exists()) {
                // dd($bookingId);

                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => 'This appointment is already cancelled.',
                ]);
            }
            //dd($bookingId);
            // 2. Validate time difference
            $bookingTime = Carbon::parse($booking->booking_date);
            $currentTime = Carbon::now();

            if ($bookingTime->lte($currentTime->subMinutes(30))) 
            {
                // 3. Send notifications to all guests
                foreach ($booking->guests as $guest) {
                    // Wrap each notification in try-catch to rollback on error
                    try {
                        $notifier->send($booking, $guest, [
                            'name' => 'Guest User',
                            'message' => route('appointments.join', $booking),
                        ]);
                    } catch (\Exception $e) {
                        DB::rollBack(); // rollback cancellation
                        Log::error("Notification failed for guest ID {$guest->id}: " . $e->getMessage());

                        return response()->json([
                            'success' => false,
                            'status' => 500,
                            'message' => 'Failed to send cancellation notification.',
                        ]);
                    }
                }

                // 4. Log cancellation only after successful notifications
                BookingCancellation::create([
                    'booking_id' => $bookingId,
                    'reason' => '',
                    'cancelled_at' => now(),
                    'is_cancellation_mail' => '1',
                ]);

                DB::commit(); // Everything successful

                return response()->json([
                    'success' => true,
                    'status' => 200,
                    'message' => 'Appointment cancelled and notifications sent.',
                ]);
            }

            return response()->json([
                'success' => false,
                'status' => 403,
                'message' => 'Appointment cancellation not allowed within 30 minutes of booking.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // In case of any exception
            Log::error('Appointment cancel failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'An unexpected error occurred while attempting to cancel the appointment.',
            ]);
        }
    }





}
