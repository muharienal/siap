<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RoomDisplay extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'type',
        'display_token',
        'orientation',
        'show_qr',
        'show_upcoming',
        'upcoming_count',
        'refresh_seconds',
        'is_active',
    ];

    protected $casts = [
        'show_qr' => 'boolean',
        'show_upcoming' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Token dibuat otomatis begitu konfigurasi pertama kali disimpan,
        // supaya admin tidak perlu mikirin ini — cukup random & panjang
        // (48 karakter) supaya praktis tidak bisa ditebak orang luar.
        static::creating(function ($display) {
            if (empty($display->display_token)) {
                $display->display_token = Str::random(48);
            }
        });
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * "Papan Semua Ruangan" — cuma boleh ada SATU baris dengan tipe ini
     * (singleton), room_id-nya selalu NULL. Dipakai untuk kondisi kantor
     * yang baru punya 1 TV digital signage untuk semua ruangan sekaligus.
     */
    public static function allRoomsBoard(): ?self
    {
        return static::where('type', 'all')->first();
    }
}