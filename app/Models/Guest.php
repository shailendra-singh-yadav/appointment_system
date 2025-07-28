<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use SoftDeletes;

    protected $fillable = ['booking_id','email','status','is_mail'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
