<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertAttributesetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'description' => 'nullable|string|min:2',
            // The book field can't be null in database. But we used the "sometimes" rule here because it has a default
            // value of 0 in database. So if no value is entered for it at all, it is considered 0 in the database.
            'order' => 'sometimes|integer|min:0',
        ];
    }
}
