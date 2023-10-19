<?php

namespace App\Http\Requests;

use App\Repositories\CouponRepo;
use App\Rules\CopounValidation;
use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class SubmitCouponRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string',
            'coupon' => new CopounValidation(),

            'order_id' => 'required_without:openOrder',
            'openOrder' => 'required_without:order_id',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('code') && $this->get('code')) {
            $this->replaceNumbers();
            $this->merge(['coupon' => CouponRepo::findCouponByCode($this->get('code'))]);
        }
        parent::prepareForValidation();
    }

    protected function replaceNumbers()
    {
        $input = $this->request->all();
        if (isset($input['code'])) {
            $input['code'] = preg_replace('/\s+/', '', $input['code']);
            $input['code'] = $this->convertToEnglish($input['code']);
        }
        $this->replace($input);
    }
}
