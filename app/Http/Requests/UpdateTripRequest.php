<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
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
        ];
    }
}
