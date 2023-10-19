<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class EditProductRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required',
            'shortName' => 'nullable|min:2|max:100',
            'basePrice' => 'required|numeric',
            'discount' => 'nullable|numeric|min:0',
            'amount' => 'required_if:amountLimit,1',
            'image' => 'image|mimes:jpeg,jpg,png',
            'file' => 'file',
            'attributeset_id' => 'required|exists:attributesets,id',
            'bonPlus' => 'nullable|numeric|min:0',
            'bonDiscount' => 'nullable|numeric|min:0|max:100',
            'parent_id' => 'nullable|numeric|min:1',
            'redirectUrl' => 'nullable|string|min:1|max:255',
            'redirectCode' => 'required_with:redirectUrl',
            'has_instalment_option' => 'nullable|boolean',
            'instalments' => [
                'nullable', 'required_with:has_instalment_option', 'array', function ($attribute, $value, $fail) {
                    $this->merge(['instalments' => array_filter($this->instalments)]);
                    if ($this->has_instalment_option && (int) array_sum($value) != 100) {
                        $fail('مجموع درصد اقساط باید برابر با ۱۰۰ باشد');
                    }
                }
            ],
        ];


        if (isset($this->request->redirectUrl)) {
            $rules['redirectCode'] = 'required|integer|in:'.implode(',',
                    array_keys(config('constants.REDIRECT_HTTP_RESPONSE_TYPES')));
        }

        return $rules;
    }

    public function prepareForValidation()
    {
        $this->replaceNumbers();

        parent::prepareForValidation();
    }

    protected function replaceNumbers()
    {
        $input = $this->request->all();

        $items = ['order', 'discount', 'amount'];
        foreach ($items as $item) {
            if (isset($input[$item])) {
                // TODO: I think the following line isn't needed. Because its task is done by validation rules.
                $input[$item] = preg_replace('/\s+/', '', $input[$item]);
                $input[$item] = $this->convertToEnglish($input[$item]);
            }
        }

        $this->replace($input);
    }
}
