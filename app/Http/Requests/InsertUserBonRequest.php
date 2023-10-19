<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertUserBonRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.INSERT_USER_BON_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'totalNumber' => 'required|numeric|min:0',
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
        if (isset($input['totalNumber'])) {
            $input['totalNumber'] = preg_replace('/\s+/', '', $input['totalNumber']);
            $input['totalNumber'] = $this->convertToEnglish($input['totalNumber']);
        }

        if (isset($input['usedNumber'])) {
            $input['usedNumber'] = preg_replace('/\s+/', '', $input['usedNumber']);
            $input['usedNumber'] = $this->convertToEnglish($input['usedNumber']);
        }

        $this->replace($input);
    }
}
