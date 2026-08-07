<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:positions,name,' . ($this->position ? $this->position->id : 'NULL') ,
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Judul posisi wajib diisi.',
            'name.string' => 'Judul posisi harus berupa teks.',
            'name.max' => 'Judul posisi tidak boleh lebih dari 255 karakter.',
            'name.unique' => 'Judul posisi sudah digunakan.',
            'description.string' => 'Deskripsi posisi harus berupa teks.',
        ];
    }
    
}
