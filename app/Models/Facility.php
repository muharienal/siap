<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'storage_location',
    ];

    public function bookingFacilities()
    {
        return $this->hasMany(BookingFacility::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_facilities')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}