<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketDepartmentRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|min:2',
            'users' => 'nullable|array',
            'users.*' => 'integer|min:1|exists:users,id',
        ];
    }
}
