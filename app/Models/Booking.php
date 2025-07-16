<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BookingCancellation;

class Booking extends Model
{
    use SoftDeletes;
   
    protected $fillable = [
        'user_id', 'title', 'description', 'booking_date', 'status','booking_email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cancellation()
    {
        return $this->hasOne(BookingCancellation::class);
    }

}
