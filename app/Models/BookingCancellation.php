<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingCancellation extends Model
{
    use SoftDeletes;
    protected $fillable = ['booking_id', 'reason', 'cancelled_at','status','cancellation_email'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
