<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RoomPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'photo_path',
        'order',
    ];

    protected $appends = ['photo_url'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            // Cek apakah file fisiknya benar-benar ada di storage/app/public/...
            if (Storage::disk('public')->exists($this->photo_path)) {
                return asset('storage/' . $this->photo_path);
            }
        }
        return null; // Kembalikan null jika file tidak ada
    }
}