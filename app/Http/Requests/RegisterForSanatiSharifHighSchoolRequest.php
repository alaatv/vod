<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class RegisterForSanatiSharifHighSchoolRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        if (auth::check()) {
            $user = auth::user();
            if (is_null($user->firstName)) {
                $rules['firstName'] = 'required';
            }

            if (is_null($user->lastName)) {
                $rules['lastName'] = 'required';
            }

            if (is_null($user->mobile)) {
                $rules['phone'] = 'required|digits:11|phone:AUTO,IR,mobile';
            }

            if (is_null($user->nationalCode)) {
                $rules['nationalCode'] = 'required|digits_between:0,15';
            }
        } else {
            $rules['firstName'] = 'required';
            $rules['lastName'] = 'required';
            $rules['phone'] = 'required|digits:11|phone:AUTO,IR,mobile';
            $rules['nationalCode'] = 'required|digits_between:0,15';
        }

        return [
            'firstName' => Arr::get($rules, 'firstName', ''),
            'lastName' => Arr::get($rules, 'lastName', ''),
            'phone' => Arr::get($rules, 'phone', ''),
            'nationalCode' => Arr::get($rules, 'nationalCode', ''),
            'grade_id' => 'required|exists:grades,id',
            'major_id' => 'required|exists:majors,id',
            'score' => [
                'required',
                'regex:/[0-9]\.*/',
            ],
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
        if (isset($input['mobile'])) {
            $input['mobile'] = preg_replace('/\s+/', '', $input['mobile']);
            $input['mobile'] = $this->convertToEnglish($input['mobile']);
        }

        if (isset($input['nationalCode'])) {
            $input['nationalCode'] = preg_replace('/\s+/', '', $input['nationalCode']);
            $input['nationalCode'] = $this->convertToEnglish($input['nationalCode']);
        }

        if (isset($input['score'])) {
            $input['score'] = preg_replace('/\s+/', '', $input['score']);
            $input['score'] = $this->convertToEnglish($input['score']);
        }

        $this->replace($input);
    }
}
