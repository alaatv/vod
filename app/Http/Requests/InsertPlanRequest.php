<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsertPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|string|date',
            'major_id' => 'nullable|numeric|exists:majors,id',
            'title' => 'nullable|string|max:190',
            'description' => 'nullable|string|max:190',
            'long_description' => 'nullable|string|max:10000',
            'start' => 'required|string|date_format:H:i:s',
            'end' => 'required|string|date_format:H:i:s|after_or_equal:start',
            'background_color' => 'nullable|string|max:20',
            'border_color' => 'nullable|string|max:20',
            'text_color' => 'nullable|string|max:20',
            'event_id' => 'required|integer|exists:studyevents,id',
            'contents' => 'array',
            'contents.*.content_id' => 'numeric|exists:educationalcontents,id',
            'contents.*.type_id' => 'numeric|exists:educationalcontent_of_plan_types,id',
            //            'contents.*.title' => 'string|max:190',
        ];
    }
}
