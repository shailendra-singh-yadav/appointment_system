<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Booking;
use App\Models\Guest;
use App\Contracts\SendAppointmentNotificationInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Services\BookingService;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
         $bookingId = $request->get('bookingId');

        // Debug to confirm
        // dd($bookingId);

        $appointments = $this->bookingService->getBookingsList($request);

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
            'filters' => [
                'bookingId' => $bookingId,
            ],
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validation failed.');
        }
        
        DB::beginTransaction();

        try {
            // Create appointment
            $appointment = Booking::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'description' => $request->description,
                'booking_date' => $request->date,
            ]);

            // Loop through guests and insert/send
            foreach ($request->guests as $email) {
                // Send email
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

            return redirect()->back()->with('success', 'Data saved successfully!');

            // return response()->json([
            //         'message' => 'Appointment and guests saved successfully!',
            //     ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Appointment booking failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');

            // return response()->json([
            //     'message' => 'Something went wrong. Please try again.',
            // ], 500);
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
            'appointment' => $appointment,
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SendAppointmentNotificationInterface $notifier)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required',
        ]);

        $appointment  = Booking::create($validated);

        foreach ($request->guests as $email) {
            $notifier->send($appointment , $email, [
                'name' => 'Guest User', // Optional
                'join_link' => route('appointments.join', $appointment ),
            ]);
        }

        return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    
    public function bookingCancel(string $id)
    {
        // dd($id);
        
    }
}
