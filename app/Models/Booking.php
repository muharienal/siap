<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'booking_type',
        'processed_by',
        'processed_at',
        'rejection_reason',
        'absent_code',
        'qr_code_path',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Employee::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(Employee::class, 'processed_by');
    }

    public function bookingFacilities()
    {
        return $this->hasMany(BookingFacility::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'booking_facilities')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}