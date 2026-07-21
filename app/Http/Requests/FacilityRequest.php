<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:facilities,name,' . ($this->facility->id ?? 'NULL') . ',id',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama fasilitas wajib diisi.',
            'name.string' => 'Nama fasilitas harus berupa teks.',
            'name.max' => 'Nama fasilitas tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Nama fasilitas sudah digunakan.',
            'description.string' => 'Deskripsi fasilitas harus berupa teks.',
        ];
    }
    
}
