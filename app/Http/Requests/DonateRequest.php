<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class DonateRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'required|integer|min:100|max:1000000000',
        ];
    }

    public function prepareForValidation()
    {
        $this->replaceNumbers();
        parent::prepareForValidation();
    }

    protected function replaceNumbers()
    {
        $input = $this->request->all();
        if (isset($input['amount'])) {
            $input['amount'] = preg_replace('/\s+/', '', $input['amount']);
            $input['amount'] = $this->convertToEnglish($input['amount']);
        }

        $this->replace($input);
    }
}
