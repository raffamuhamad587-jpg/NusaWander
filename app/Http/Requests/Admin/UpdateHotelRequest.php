<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRequest extends StoreHotelRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'bintang' => 'required|integer|min:1|max:5',
        ]);
    }
}
