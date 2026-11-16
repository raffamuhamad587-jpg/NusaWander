<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'nama' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'jam_operasional' => 'nullable|string|max:100',
            'estimasi_harga' => 'required|numeric|min:0',
            'jenis_harga' => 'required|in:per_orang,per_kelompok',
            'status' => 'required|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori destinasi wajib dipilih.',
            'nama.required' => 'Nama destinasi wajib diisi.',
            'deskripsi.required' => 'Deskripsi destinasi wajib diisi.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'kota.required' => 'Nama kota/kabupaten wajib diisi.',
            'estimasi_harga.required' => 'Estimasi harga wajib diisi.',
        ];
    }
}