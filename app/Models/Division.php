<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $table = 'divisions';

    protected $fillable = [
        'name',
    ];

    // Divisi punya banyak Bidang (hierarki: Divisi -> Bidang -> Pengguna)
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    // Relationships
    public function employees()
    {
        return $this->hasMany(User::class);
    }
}