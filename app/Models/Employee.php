<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'employees';

    protected $fillable = [
        'nip',
        'full_name',
        'division_id',
        'position_id',
        'email',
        'phone_number',
        'password',
        'role',
        'is_active',
        'remember_token',
        'gender',
        'birth_date',
        'address',
        'employment_status',
        'photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }


    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
}