<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tipe' => 'required|string|max:120',
            'bintang' => 'required|integer|min:1|max:5',
            'harga_min' => 'required|numeric|min:0',
            'harga_max' => 'required|numeric|min:0|gte:harga_min',
            'rating' => 'required|numeric|min:0|max:5',
            'jumlah_ulasan' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama hotel wajib diisi.',
            'deskripsi.required' => 'Deskripsi hotel wajib diisi.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'kota.required' => 'Nama kota/kabupaten wajib diisi.',
            'tipe.required' => 'Tipe akomodasi wajib diisi.',
            'bintang.required' => 'Bintang hotel wajib dipilih.',
            'harga_min.required' => 'Harga termurah wajib diisi.',
            'harga_max.required' => 'Harga termahal wajib diisi.',
            'harga_max.gte' => 'Harga termahal harus lebih besar atau sama dengan harga termurah.',
            'rating.required' => 'Rating wajib diisi.',
            'image.image' => 'File harus berupa gambar.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
