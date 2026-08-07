<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingFacility extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'facility_id',
        'quantity',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

}
