<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDestinationImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_id' => 'required|exists:destinations,id',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'keterangan' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'destination_id.required' => 'Destinasi wajib dipilih.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.max' => 'Ukuran file gambar maksimal 2MB.',
        ];
    }
}