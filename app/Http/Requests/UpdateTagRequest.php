<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required_without:value|nullable|string|min:2|unique:tags,name,'.$this->tag->id.',id,deleted_at,NULL',
            'value' => 'required_without:name|nullable|string|min:2|unique:tags,value,'.$this->tag->id.',id,deleted_at,NULL',
            'tag_group_id' => 'required|integer|min:1|exists:tag_groups,id',
            'enable_exp' => 'nullable',
            'description' => 'nullable|string|min:2',
        ];
    }
}
