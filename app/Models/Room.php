<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'location',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function activeBookings()
    {
        return $this->hasMany(Booking::class)->whereIn('status', [0, 1]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function photos()
    {
        return $this->hasMany(RoomPhoto::class)->orderBy('order');
    }
}