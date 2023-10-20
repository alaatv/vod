<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertVoucherRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'postalCode' => 'required|numeric',
            'email' => 'sometimes|nullable|email',
            'gender_id' => 'required|exists:genders,id',
            'province' => 'required',
            'city' => 'required',
            'address' => 'required',
            'birthdate' => 'required',
            'school' => 'required',
            'major_id' => 'required|exists:majors,id',
            'introducedBy' => 'required',
        ];

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
        if (isset($input['email'])) {
            $input['email'] = preg_replace('/\s+/', '', $input['email']);
            $input['email'] = $this->convertToEnglish($input['email']);
        }
        if (isset($input['postalCode'])) {
            $input['postalCode'] = preg_replace('/\s+/', '', $input['postalCode']);
            $input['postalCode'] = $this->convertToEnglish($input['postalCode']);
        }

        if (isset($input['address'])) {
            $input['address'] = $this->convertToEnglish($input['address']);
        }

        if (isset($input['school'])) {
            $input['school'] = $this->convertToEnglish($input['school']);
        }

        if (isset($input['introducedBy'])) {
            $input['introducedBy'] = $this->convertToEnglish($input['introducedBy']);
        }
        $this->replace($input);
    }
}
