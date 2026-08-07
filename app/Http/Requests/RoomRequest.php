<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // atau bisa ditambahkan logic authorization jika diperlukan
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255', 
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
        ];

        // Jika ini adalah update request, tambahkan unique rule dengan exception untuk ID saat ini
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] .= '|unique:rooms,name,' . $this->route('room');
        } else {
            // Untuk create request
            $rules['name'] .= '|unique:rooms,name';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama ruangan wajib diisi.',
            'name.string' => 'Nama ruangan harus berupa teks.',
            'name.max' => 'Nama ruangan tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama ruangan sudah digunakan.',
            'capacity.required' => 'Kapasitas ruangan wajib diisi.',
            'capacity.integer' => 'Kapasitas ruangan harus berupa angka.',
            'capacity.min' => 'Kapasitas ruangan harus minimal 1.',
        ];
    }
}
