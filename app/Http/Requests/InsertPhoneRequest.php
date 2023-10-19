<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertPhoneRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.INSERT_CONTACT_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'phoneNumber' => 'required|numeric',
            'priority' => 'numeric',
            'contact_id' => 'exists:contacts,id',
            'phonetype_id' => 'exists:phonetypes,id',
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
        if (isset($input['phoneNumber'])) {
            $input['phoneNumber'] = preg_replace('/\s+/', '', $input['phoneNumber']);
            $input['phoneNumber'] = $this->convertToEnglish($input['phoneNumber']);
        }

        if (isset($input['priority'])) {
            $input['priority'] = preg_replace('/\s+/', '', $input['priority']);
            $input['priority'] = $this->convertToEnglish($input['priority']);
        }
        $this->replace($input);
    }
}
