<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kategori' => 'required|string|max:100|unique:categories,nama_kategori,' . $this->route('category')->id,
            'deskripsi' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.unique' => 'Nama kategori ini sudah terdaftar.',
            'nama_kategori.max' => 'Nama kategori maksimal 100 karakter.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
        ];
    }
}