<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
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
                function ($attribute, $value, $fail) {
                    $this->validateTimeAvailability($attribute, $value, $fail);
                },
            ],
            'end_time' => [
                'required',
                'date', 
                'after:start_time',
                function ($attribute, $value, $fail) {
                    $this->validateTimeAvailability($attribute, $value, $fail);
                },
            ],
            'purpose' => 'required|string|min:10|max:500',
            'facilities' => 'sometimes|array',
            'facilities.*' => 'exists:facilities,id',
            'quantities' => 'sometimes|array',
            'quantities.*' => 'integer|min:1|max:100',
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

        $isPriorityAttempt = Auth::check()
            && Auth::user()->role == 1
            && $this->input('booking_type') === 'Priority';

        $query = Booking::where('room_id', $roomId)
            ->where('status', 1) // Only check approved bookings
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                      ->orWhereBetween('end_time', [$start_time, $end_time])
                      ->orWhere(function ($q) use ($start_time, $end_time) {
                          $q->where('start_time', '<=', $start_time)
                            ->where('end_time', '>=', $end_time);
                      });
            });

        // Priority mengalahkan Regular (approved sekalipun) — jadi hanya dicegat
        // kalau bentrok dengan Priority lain yang sudah disetujui.
        if ($isPriorityAttempt) {
            $query->where('booking_type', 'Priority');
        }

        // Exclude current booking when updating
        if ($this->route('booking')) {
            $query->where('id', '!=', $this->route('booking')->id);
        }

        $conflictingBooking = $query->exists();

        if ($conflictingBooking) {
            $fail($isPriorityAttempt
                ? 'Sudah ada peminjaman Priority lain yang disetujui pada ruangan & waktu ini.'
                : 'Ruangan sudah dipesan pada waktu tersebut. Silakan pilih waktu lain.');
        }
    }
}