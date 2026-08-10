<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'division_id',
        'name',
    ];

    // Bidang dimiliki oleh satu Divisi (hierarki: Divisi -> Bidang -> Pengguna)
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Relationships
    public function employees()
    {
        return $this->hasMany(User::class);
    }
}