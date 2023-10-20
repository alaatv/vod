<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateContentSetRequest
 * @package App\Http\Requests
 */
class ConfirmEmployeeOvertimeRequest extends FormRequest
{
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
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'min:1', 'exists:users,id,deleted_at,NULL'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'overtime_status_id' => ['required', 'exists:employeeovertimestatus,id,deleted_at,NULL'],
        ];
    }
}
