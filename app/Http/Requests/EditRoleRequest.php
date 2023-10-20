<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditRoleRequest extends FormRequest
{
    public function authorize()
    {
        if (auth()
            ->user()
            ->hasRole('admin')) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'display_name' => 'required',
            'permissions' => 'exists:permissions,id',
        ];
    }
}
