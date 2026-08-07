<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nip',
        'full_name',
        'division_id',
        'position_id',
        'gender',
        'birth_date',
        'phone_number',
        'email',
        'address',
        'employment_status',
        'photo_path',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi ke Divisi
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Relasi ke Position
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    // Relasi ke Bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }


    // Relasi ke Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Login menggunakan NIK sudah dihandle di LoginController (username() + Auth::attempt)
}