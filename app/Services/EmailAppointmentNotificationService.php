<?php

namespace App\Services;

use App\Contracts\SendAppointmentNotificationInterface;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentInvite;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EmailAppointmentNotificationService implements SendAppointmentNotificationInterface
{
    public function send(Appointment $appointment, string $to, array $data = []): void
    {
        // dd($data);
        $guestName = $data['name'] ?? 'Guest';
        $joinLink = $data['join_link'] ?? route('appointments.join', $appointment);
        $appointmentUTC = $appointment->date; // stored in UTC
        $userTimezone = $user->timezone ?? 'Asia/Kolkata';

        $localizedDate = Carbon::parse($appointmentUTC)
            ->timezone($userTimezone)
            ->format('l, d M Y h:i A');

        // Mail::to($to)->queue(
        //     new AppointmentInvite($appointment, $guestName, $joinLink)
        // );

        
        //without mailable class
        $send = Mail::raw("Hello $guestName, your appointment is scheduled on {$appointment->date}. Join here: $joinLink", function ($message) use ($appointment) {
            $message->to('yadav.shailendra1990@gmail.com')
                    ->subject('Appointment Invite');
        });

        if($send){
             Log::info("Appointment email sent ");
        }else{
            Log::info("Appointment email not sent ");
        }
    }
}
