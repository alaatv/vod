<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class EditAssignmentRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        if (auth()
            ->user()
            ->isAbleTo(config('constants.EDIT_ASSIGNMENT_ACCESS'))) {
            return true;
        }

        return false;
    }

    public function rules()
    {
        return [
            'questionFile' => 'file|mimes:pdf,rar,zip',
            'solutionFile' => 'file|mimes:pdf,rar,zip',
            'majors' => 'required|exists:majors,id',
            'assignmentstatus_id' => 'required|exists:assignmentstatuses,id',
            'numberOfQuestions' => 'integer|min:1',
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
        if (isset($input['numberOfQuestions'])) {
            $input['numberOfQuestions'] = preg_replace('/\s+/', '', $input['numberOfQuestions']);
            $input['numberOfQuestions'] = $this->convertToEnglish($input['numberOfQuestions']);
        }

        if (isset($input['order'])) {
            $input['order'] = preg_replace('/\s+/', '', $input['order']);
            $input['order'] = $this->convertToEnglish($input['order']);
        }
        $this->replace($input);
    }
}
