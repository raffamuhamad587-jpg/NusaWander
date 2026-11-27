<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'itinerary_item_id' => ['nullable', 'integer', 'exists:itinerary_items,id'],
            'nama_biaya' => ['required', 'string', 'max:150'],
            'jenis_biaya' => ['required', 'in:tiket,transportasi,penginapan,kuliner,lainnya'],
            'dasar_perhitungan' => ['required', 'in:per_orang,per_kelompok,per_hari,tetap'],
            'jumlah' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'harga_satuan' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'belum_dihitung' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_biaya.required' => 'Nama komponen biaya wajib diisi.',
            'jenis_biaya.required' => 'Jenis biaya wajib dipilih.',
            'dasar_perhitungan.required' => 'Dasar perhitungan biaya wajib dipilih.',
        ];
    }
}
