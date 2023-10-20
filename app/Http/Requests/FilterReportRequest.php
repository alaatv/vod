<?php

namespace App\Http\Requests;

use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterReportRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'creator' => ['sometimes', Rule::when($this->creator != 'all', Rule::exists('users', 'id'))],
            'month' => [
                'sometimes', Rule::in(array_merge(array_column(config('constants.JALALI_CALENDER'), 'month'), ['all']))
            ],
//            'order' => ['sometimes', Rule::in(array_merge(array_keys(Report::AUDIT_ORDERS), ['all']))],
            'type' => ['sometimes', Rule::when($this->type != 'all', Rule::exists('report_types', 'id'))],
            'gateway' => ['sometimes', Rule::when($this->gateway != 'all', Rule::exists('transactiongateways', 'id'))],
            'report_status' => [
                'sometimes', Rule::when($this->report_status != 'all', Rule::exists('report_statuses', 'id'))
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $accessibleTypes = Report::getAccessibleTypes();
        $typeFromRequest = $this->type ? [$this->type] : [];
        $this->merge([
            'type' => array_merge($typeFromRequest, $accessibleTypes)
        ]);
    }
}
