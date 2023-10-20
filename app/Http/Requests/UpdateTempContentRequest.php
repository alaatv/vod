<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTempContentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        $result = [];
        foreach ($this->except(['_token', '_method']) as $key => $input) {
            if ($key == 'accept') {
                $result[$key] = ['date_format:"Y-m-d H:i:s"', 'nullable'];
                continue;
            }
            $result[$key] = ['string', 'max:10'];
        }
        return $result;
    }
}
