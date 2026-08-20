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
        'consumption_qty',
        'other_notes',
        'status',
        'booking_type',
        'displaced_by_id',
        'relocation_note',
        'awaiting_user_confirmation',
        'relocated_from_room_id',
        'relocated_from_start_time',
        'relocated_from_end_time',
        'relocated_at',
        'processed_by',
        'processed_at',
        'rejection_reason',
        'approval_note',
        'absent_code',
        'qr_code_path',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'processed_at' => 'datetime',
        'awaiting_user_confirmation' => 'boolean',
        'relocated_from_start_time' => 'datetime',
        'relocated_from_end_time' => 'datetime',
        'relocated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
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

    // Booking Priority yang menyebabkan booking ini bentrok (jika ada)
    public function displacedBy()
    {
        return $this->belongsTo(Booking::class, 'displaced_by_id');
    }

    // Booking-booking lain yang tergeser akibat booking Priority ini
    public function displaces()
    {
        return $this->hasMany(Booking::class, 'displaced_by_id');
    }

    // Ruangan asal sebelum booking ini dialihkan admin (riwayat pengalihan
    // terakhir, null kalau booking ini belum pernah dialihkan sama sekali)
    public function relocatedFromRoom()
    {
        return $this->belongsTo(Room::class, 'relocated_from_room_id');
    }
}