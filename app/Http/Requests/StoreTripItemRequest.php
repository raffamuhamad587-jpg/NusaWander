<?php

namespace App\Http\Requests;

use App\Models\Trip;
use Illuminate\Foundation\Http\FormRequest;

class StoreTripItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        /** @var Trip|null $trip */
        $trip = $this->route('trip');
        $maxDay = $trip ? max(1, (int) $trip->durasi_hari) : 30;

        return [
            'destination_id' => ['required', 'integer', 'exists:destinations,id'],
            'hari_ke' => ['required', 'integer', 'min:1', "max:{$maxDay}"],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        /** @var Trip|null $trip */
        $trip = $this->route('trip');
        $maxDay = $trip ? (int) $trip->durasi_hari : 30;

        return [
            'destination_id.required' => 'Destinasi wajib dipilih.',
            'hari_ke.required' => 'Hari kunjungan wajib dipilih.',
            'hari_ke.max' => "Hari kunjungan tidak boleh melebihi durasi trip ({$maxDay} hari).",
        ];
    }
}
