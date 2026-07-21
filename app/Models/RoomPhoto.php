<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }
}