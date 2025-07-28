<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Booking;
use App\Models\Guest;
use App\Contracts\SendAppointmentNotificationInterface;
use Illuminate\Support\Facades\Validator;
use App\Services\BookingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;


class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $bookingService;
    protected $notifier;

    public function __construct(BookingService $bookingService, SendAppointmentNotificationInterface $notifier)
    {
        $this->bookingService = $bookingService;
        $this->notifier = $notifier;
    }

    public function index(Request $request)
    {
        $bookingId = $request->get('bookingId');
        $appointments = $this->bookingService->getBookingsList($request);

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'filters' => [
                'bookingId' => $bookingId,
            ],
            'section' => !empty($bookingId) ? 'guest' : '',
        ]);

    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Appointments/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
   
    public function store(Request $request, SendAppointmentNotificationInterface $notifier)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'guests' => 'required|array|min:1',
            'guests.*' => 'required|email',
            'reminder_preference' => 'nullable|string|in:10_minutes,30_minutes,1_hour,6_hours,1_day',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed.');
        }

        try {
            $response = $this->bookingService->createBooking($request, $notifier);
            return redirect()->back()->with('success', $response['message']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function join(string $id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $appointment)
    {
        // dd($appointment);
        return Inertia::render('Appointments/Create', [
            'appointment' => $appointment->load('guests'),  //guests relationship load
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:bookings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'guests' => 'required|array|min:1',
            'guests.*' => 'required|email',
        ]);

        try {
            $response = $this->bookingService->updateBooking($request, $this->notifier);
            return redirect()->back()->with('success', $response['message']);
        } 
        catch (\Exception $e) {
            Log::error('Appointment update failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $appointment)
    {
       // dd($appointment->id);
        // Delete all related guests
        $appointment->guests()->delete();

        // Optionally delete cancellation record too
        $appointment->cancellation()->delete();

        // Soft-delete the booking itself
        $appointment->delete();

        return redirect()->route('appointments.index')
                        ->with('success', 'Booking and related records deleted successfully.');
    }


    
    public function appointmentCancel(Booking $booking)
    {
        if(empty($booking->id)){
            return redirect()->route('appointments.index')->with('error', 'Booking ID Must not Empty!');
        }

        $response = $this->bookingService->appointmentCancel($booking->id, $this->notifier);
        $data = $response->getData();  // This gives you stdClass object from JsonResponse
      
        return response()->json([
            'success' => $data->success,
            'message' => $data->message,
        ]);
   
    }



}
