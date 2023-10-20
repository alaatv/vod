<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use App\Traits\UserCommon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class InsertUserRequest extends FormRequest
{
    use CharacterCommon;
    use UserCommon;

    public function authorize()
    {
        if (Auth::check() && Auth::user()->isAbleTo(config('constants.INSERT_USER_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return $this->getInsertUserValidationRules($this->request->all());
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
        if (isset($input['postalCode'])) {
            $input['postalCode'] = preg_replace('/\s+/', '', $input['postalCode']);
            $input['postalCode'] = $this->convertToEnglish($input['postalCode']);
        }
        if (isset($input['nationalCode'])) {
            $input['nationalCode'] = preg_replace('/\s+/', '', $input['nationalCode']);
            $input['nationalCode'] = $this->convertToEnglish($input['nationalCode']);
        }
        if (isset($input['password'])) {
            $input['password'] = $this->convertToEnglish($input['password']);
        }
        if (isset($input['email'])) {
            $input['email'] = preg_replace('/\s+/', '', $input['email']);
            $input['email'] = $this->convertToEnglish($input['email']);
        }
        $this->replace($input);
    }
}
