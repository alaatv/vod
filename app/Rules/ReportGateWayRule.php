<?php

namespace App\Rules;

use App\Models\Report;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;

class ReportGateWayRule implements Rule, DataAwareRule
{

    protected $data = [];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !Report::query()->where('title', $this->data['report_title'])
            ->whereNotNull('data')
            ->where('data->gateway_id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.custom.report_gateway.duplication');
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
