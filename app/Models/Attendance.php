<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'guest_name',
        'check_in_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}