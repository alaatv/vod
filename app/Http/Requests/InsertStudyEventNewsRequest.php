<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertStudyEventNewsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|min:3|max:191',
            'body' => 'required|string|min:3',
            'studyevent_id' => 'required|exists:studyevents,id',
        ];
    }
}
