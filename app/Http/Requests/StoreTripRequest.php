<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nama_trip' => ['required', 'string', 'max:150'],
            'wilayah_tujuan' => ['required', 'string', 'max:100'],
            'tanggal_mulai' => ['required', 'date'],
            'durasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
            'jumlah_peserta' => ['required', 'integer', 'min:1', 'max:100'],
            'anggaran' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'kategori_ids' => ['nullable', 'array'],
            'kategori_ids.*' => ['integer', 'exists:categories,id'],
            'prioritas_biaya' => ['required', 'in:hemat,seimbang,nyaman'],
            'destination_id' => ['nullable', 'integer', 'exists:destinations,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_trip.required' => 'Nama rencana perjalanan wajib diisi.',
            'wilayah_tujuan.required' => 'Wilayah atau kota tujuan wajib diisi.',
            'tanggal_mulai.required' => 'Tanggal mulai perjalanan wajib dipilih.',
            'durasi_hari.required' => 'Durasi hari wajib diisi.',
            'durasi_hari.min' => 'Durasi minimal 1 hari.',
            'durasi_hari.max' => 'Durasi maksimal 30 hari.',
            'jumlah_peserta.required' => 'Jumlah peserta wajib diisi.',
            'anggaran.required' => 'Anggaran perjalanan wajib diisi.',
            'prioritas_biaya.required' => 'Prioritas alokasi biaya wajib dipilih.',
        ];
    }
}
