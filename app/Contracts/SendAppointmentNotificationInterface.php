<?php

namespace App\Contracts;

use App\Models\Appointment;

interface SendAppointmentNotificationInterface
{
    public function send(Appointment $appointment, string $to, array $data = []): void;
}

