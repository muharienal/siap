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
        'consumption_notes',
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

    /*
    |--------------------------------------------------------------------------
    | ATURAN BAKU: "Ditolak" vs "Dibatalkan"
    |--------------------------------------------------------------------------
    | Keduanya disimpan sebagai status = 2 (tidak ada status 3). Pembedanya
    | adalah kolom absent_code:
    |
    |  - DITOLAK    : status 2 & absent_code KOSONG. Pengajuan TIDAK PERNAH
    |                 disetujui. Penyebab: (a) admin menolak saat Pending, atau
    |                 (b) peminjam menolak usulan pengalihan dari admin.
    |  - DIBATALKAN : status 2 & absent_code TERISI. Booking SEMPAT disetujui
    |                 (kode presensi pernah dibuat), lalu persetujuannya dicabut
    |                 admin lewat "Batalkan Persetujuan".
    |
    | Kenapa absent_code bisa jadi pembeda: kolom ini hanya diisi saat
    | approve()/bulk approve/confirmRelocation(), dibiarkan saat dibatalkan
    | (reject() tidak menghapusnya), dan DIKOSONGKAN saat relocate() — jadi
    | booking yang dialihkan lalu ditolak peminjam tetap terbaca "Ditolak".
    |
    | SEMUA tampilan/pesan/ekspor/statistik wajib memakai tiga accessor di
    | bawah ini, jangan menulis ulang rumus status di tempat lain.
    */

    // true = status 2 karena persetujuan DIBATALKAN (bukan ditolak dari awal)
    public function getWasCancelledApprovalAttribute(): bool
    {
        return (int) $this->status === 2 && !empty($this->absent_code);
    }

    // Kunci status untuk kelas CSS: pending | approved | rejected | cancelled
    public function getStatusKeyAttribute(): string
    {
        return match ((int) $this->status) {
            0 => 'pending',
            1 => 'approved',
            default => $this->was_cancelled_approval ? 'cancelled' : 'rejected',
        };
    }

    // Label status untuk ditampilkan: Pending | Disetujui | Ditolak | Dibatalkan
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_key) {
            'pending' => 'Pending',
            'approved' => 'Disetujui',
            'cancelled' => 'Dibatalkan',
            default => 'Ditolak',
        };
    }
}