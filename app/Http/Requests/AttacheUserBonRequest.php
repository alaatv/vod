<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class AttacheUserBonRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.ATTACHE_USER_BON_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'totalNumber' => 'required|numeric',
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
        $this->replace($input);
    }
}
