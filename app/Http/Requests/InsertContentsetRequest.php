<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertContentsetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'author_id' => 'nullable|exists:users,id'
        ];
    }
}
