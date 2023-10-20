<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertConsultationRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.INSERT_CONSULTATION_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'majors' => 'required|exists:majors,id',
            'consultationstatus_id' => 'required|exists:consultationstatuses,id',
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
        if (isset($input['order'])) {
            $input['order'] = preg_replace('/\s+/', '', $input['order']);
            $input['order'] = $this->convertToEnglish($input['order']);
        }
        $this->replace($input);
    }
}
