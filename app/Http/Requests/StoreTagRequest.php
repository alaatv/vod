<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required_without:value|nullable|string|min:2|unique:tags,name,NULL,id,deleted_at,NULL',
            'value' => 'required_without:name|nullable|alpha_dash|min:2|unique:tags,value,NULL,id,deleted_at,NULL',
            'tag_group_id' => 'required|integer|min:1|exists:tag_groups,id',
            'enable_exp' => 'nullable',
            'description' => 'nullable|string|min:2',
        ];
    }
}
