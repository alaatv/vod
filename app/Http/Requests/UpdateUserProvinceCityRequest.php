<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProvinceCityRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'province_id' => 'integer|min:1|exists:ostan,id',
            'city_id' => 'integer|min:1|exists:shahr,id',
        ];
    }
}
