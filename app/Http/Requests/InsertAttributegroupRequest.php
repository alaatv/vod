<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertAttributegroupRequest extends FormRequest
{

    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.INSERT_ATTRIBUTEGROUP_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
