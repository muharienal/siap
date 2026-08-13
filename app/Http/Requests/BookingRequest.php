<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Booking;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Combine start_date with start_time/end_time into full datetimes
     * before the rules() below run, since the form sends them as
     * separate fields (date input + two time-only inputs).
     */
    protected function prepareForValidation(): void
    {
        $date = $this->input('start_date');

        if ($date) {
            $merged = [];

            if ($this->filled('start_time') && !str_contains($this->input('start_time'), $date)) {
                $merged['start_time'] = $date . ' ' . $this->input('start_time') . ':00';
            }

            if ($this->filled('end_time') && !str_contains($this->input('end_time'), $date)) {
                $merged['end_time'] = $date . ' ' . $this->input('end_time') . ':00';
            }

            if (!empty($merged)) {
                $this->merge($merged);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'start_time' => [
                'required',
                'date',
                'after:now',
            ],
            'end_time' => [
                'required',
                'date', 
                'after:start_time',
                // FIX: sebelumnya validateTimeAvailability() dipasang di
                // KEDUA field (start_time & end_time) — kalau memang ada
                // bentrok, $fail() terpanggil 2x dengan pesan identik,
                // makanya pesan "Ruangan sudah dipesan..." muncul dobel.
                // Cukup dijalankan sekali di sini.
                function ($attribute, $value, $fail) {
                    $this->validateTimeAvailability($attribute, $value, $fail);
                },
            ],
            'purpose' => 'required|string|min:10|max:500',
            'facilities' => 'sometimes|array',
            'facilities.*' => 'exists:facilities,id',
            'quantities' => 'sometimes|array',
            'quantities.*' => 'integer|min:1|max:100',

            // Kebutuhan tambahan (checkbox "Konsumsi" & "Lain-lain (Catatan)")
            'needs_consumption' => 'sometimes|boolean',
            'consumption_qty' => 'required_if:needs_consumption,1|nullable|integer|min:1|max:1000',
            'needs_other' => 'sometimes|boolean',
            'other_notes' => 'required_if:needs_other,1|nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'room_id.required' => 'Ruangan harus dipilih.',
            'room_id.exists' => 'Ruangan yang dipilih tidak valid.',
            'start_time.required' => 'Tanggal dan waktu mulai harus diisi.',
            'start_time.date' => 'Format tanggal dan waktu mulai tidak valid.',
            'start_time.after' => 'Tanggal dan waktu mulai harus setelah waktu sekarang.',
            'end_time.required' => 'Tanggal dan waktu selesai harus diisi.',
            'end_time.date' => 'Format tanggal dan waktu selesai tidak valid.',
            'end_time.after' => 'Tanggal dan waktu selesai harus setelah waktu mulai.',
            'purpose.required' => 'Tujuan peminjaman harus diisi.',
            'purpose.min' => 'Tujuan peminjaman minimal 10 karakter.',
            'purpose.max' => 'Tujuan peminjaman maksimal 500 karakter.',
            'facilities.*.exists' => 'Fasilitas yang dipilih tidak valid.',
            'quantities.*.integer' => 'Jumlah fasilitas harus berupa angka.',
            'quantities.*.min' => 'Jumlah fasilitas minimal 1.',
            'quantities.*.max' => 'Jumlah fasilitas maksimal 100.',

            'consumption_qty.required_if' => 'Jumlah peserta konsumsi harus diisi.',
            'consumption_qty.integer' => 'Jumlah peserta konsumsi harus berupa angka.',
            'consumption_qty.min' => 'Jumlah peserta konsumsi minimal 1.',
            'other_notes.required_if' => 'Catatan kebutuhan lain-lain harus diisi.',
            'other_notes.max' => 'Catatan kebutuhan lain-lain maksimal 1000 karakter.',
        ];
    }

    /**
     * Validate time availability
     */
    private function validateTimeAvailability($attribute, $value, $fail)
    {
        $roomId = $this->input('room_id');
        $start_time = $this->input('start_time');
        $end_time = $this->input('end_time');
        
        if (!$roomId || (!$start_time && !$end_time)) {
            return;
        }

        // Skip validation if both times are not set yet
        if ($attribute === 'start_time' && !$end_time) {
            return;
        }
        
        if ($attribute === 'end_time' && !$start_time) {
            return;
        }

        // FIX: sebelumnya di sini cuma cek status Disetujui (1), padahal
        // cek ketersediaan live (checkAvailability, indikator hijau di
        // form) mengecek Pending DAN Disetujui (status 0 & 1). Beda kriteria
        // ini bisa bikin indikator bilang "tersedia" tapi submit-nya malah
        // ditolak "sudah dipesan", atau sebaliknya. Disamakan supaya kedua
        // pengecekan selalu sepakat.
        //
        // FIX LEBIH BESAR: query overlap sebelumnya pakai whereBetween di
        // start_time DAN end_time — whereBetween di Laravel/SQL itu
        // INKLUSIF di kedua ujungnya. Akibatnya, booking yang berakhir
        // TEPAT jam 14:00 dianggap "bentrok" dengan booking baru yang
        // MULAI tepat jam 14:00 juga — padahal itu cuma bersambungan,
        // bukan tumpang tindih. Diganti rumus overlap standar & benar:
        // dua rentang waktu tumpang tindih HANYA JIKA
        // (mulai_lama < selesai_baru) DAN (selesai_lama > mulai_baru).
        // Rumus ini otomatis memperbolehkan booking yang bersambungan
        // persis di batas waktu (back-to-back).
        $query = Booking::where('room_id', $roomId)
            ->whereIn('status', [0, 1])
            ->where('start_time', '<', $end_time)
            ->where('end_time', '>', $start_time);

        // Sistem konflik prioritas: booking Priority (Sesdir/Admin) boleh
        // menembus bentrok dengan booking Reguler yang sudah ada — booking
        // Reguler itu akan ditandai "bentrok" dan dialihkan admin setelahnya.
        // Priority vs Priority dan Reguler vs Reguler tetap diblokir seperti biasa.
        if ($this->resolveBookingType() === 'Priority') {
            $query->where('booking_type', 'Priority');
        }

        // Exclude current booking when updating
        if ($this->route('booking')) {
            $query->where('id', '!=', $this->route('booking')->id);
        }

        $conflictingBooking = $query->exists();

        if ($conflictingBooking) {
            $fail('Ruangan sudah dipesan pada waktu tersebut. Silakan pilih waktu lain.');
        }
    }

    /**
     * Tentukan booking_type yang sebenarnya akan disimpan, mengikuti aturan
     * yang sama dengan BookingController::store()/update():
     * - Karyawan (role 2): selalu Regular, apa pun input yang dikirim.
     * - Sesdir (role 3): selalu Priority.
     * - Admin (role 1): mengikuti pilihan eksplisit di form (default Priority
     *   jika tidak dikirim / tidak valid).
     * Dipakai di sini supaya validasi bentrok konsisten dengan apa yang
     * benar-benar akan tersimpan ke database.
     */
    private function resolveBookingType(): string
    {
        $userRole = auth()->user()->role ?? 2;

        if ($userRole == 2) {
            return 'Regular';
        }
        if ($userRole == 3) {
            return 'Priority';
        }

        return in_array($this->input('booking_type'), ['Regular', 'Priority'])
            ? $this->input('booking_type')
            : 'Priority';
    }
}