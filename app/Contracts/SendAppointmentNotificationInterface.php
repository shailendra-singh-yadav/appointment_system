<?php

namespace App\Contracts;

use App\Models\Booking;

interface SendAppointmentNotificationInterface
{
    public function send(Booking $appointment, string $to, array $data = []): void;
}

