<?php

namespace App\Rules;

use App\Traits\DateTrait;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;

class AuditReportRule implements Rule, ValidatorAwareRule
{
    use DateTrait;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value === 'audit') {
            $jalaliDate = explode('/', $this->convertDate(now('Asia/Tehran')->format('Y-m-d'), 'toJalali'));
            $currentDay = $jalaliDate[2];
            $currentMonth = $jalaliDate[1];
            $requestedMonth = $this->convertToJalaliMonth($this->data['report_month'], 'STRING_TO_NUMBER');

            if ($requestedMonth < $currentMonth) {
                return true;
            }
            if ($requestedMonth > $currentMonth) {
                return false;
            }
            if ($this->data['report_order'] == 1 && $currentDay >= 15) {
                return true;
            } elseif ($this->data['report_order'] == 1 && $currentDay < 15) {
                return false;
            } elseif ($this->data['report_order'] == 2 && in_array($this->data['report_month'],
                    ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور']) && $currentDay < 31) {
                return false;
            } elseif ($this->data['report_order'] == 2 && in_array($this->data['report_month'],
                    ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور']) && $currentDay == 31) {
                return true;
            } elseif ($this->data['report_order'] == 2 && in_array($this->data['report_month'],
                    ['مهر', 'آبان', 'آذر', 'دی', 'بهمن',]) && $currentDay < 30) {
                return false;
            } elseif ($this->data['report_order'] == 2 && in_array($this->data['report_month'],
                    ['مهر', 'آبان', 'آذر', 'دی', 'بهمن',]) && $currentDay == 30) {
                return true;
            } elseif ($this->data['report_order'] == 2 && $this->data['report_month'] == 'اسفند' && $currentDay < 29) {
                return false;
            }

        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.audit_report_is_soon');
    }

    public function setValidator($validator)
    {
        $this->data = $validator->attributes();

        return $this;
    }

}
