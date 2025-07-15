<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Message.php
class Message extends Model
{
    protected $fillable = ['user_id', 'body'];
    public function user() { 
        return $this->belongsTo(User::class);
    }
}

