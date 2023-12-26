<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateReportRequest extends FormRequest
{
    public function authorize()
    {
        return true;
        //        $permission = Report::permissionMaker($this->get('action'));
        //        return auth()?->user()?->hasPermission($permission);
    }

    public function rules()
    {
        return [
            //            'report_type' => ['required', 'exists:report_types,id'],
            'from' => ['required', 'date'],
            'to' => ['required', 'date'],
            'gateway' => ['required'],
            'report_title' => ['required'],
        ];
    }
}
