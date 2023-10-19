<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class EditTransactionRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $transaction = $this->route('transaction');
        $transactionId = $transaction->id;
        $rules = [
            'cost' => 'required|integer',
            'referenceNumber' => 'unique:transactions,referenceNumber,'.$transactionId.',id,deleted_at,NULL|nullable',
            'traceNumber' => 'unique:transactions,traceNumber,'.$transactionId.',id,deleted_at,NULL|numeric|nullable',
            'transactionID' => 'unique:transactions,transactionID,'.$transactionId.',id,deleted_at,NULL|nullable',
            'authority' => 'unique:transactions,authority,'.$transactionId.',id,deleted_at,NULL|nullable',
            'paycheckNumber' => [
//                'unique:transactions,paycheckNumber,' . $transaction->id . ',id,deleted_at,NULL',
                'nullable'
            ],
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
        if (isset($input['cost'])) {
            $input['cost'] = preg_replace('/\s+/', '', $input['cost']);
            if (strlen($input['cost']) > 0) {
                $input['cost'] = $this->convertToEnglish($input['cost']);
            }
        }

        if (isset($input['referenceNumber'])) {
            $input['referenceNumber'] = preg_replace('/\s+/', '', $input['referenceNumber']);
            if (strlen($input['referenceNumber']) > 0) {
                $input['referenceNumber'] = $this->convertToEnglish($input['referenceNumber']);
            }
        }

        if (isset($input['traceNumber'])) {
            $input['traceNumber'] = preg_replace('/\s+/', '', $input['traceNumber']);
            if (strlen($input['traceNumber']) > 0) {
                $input['traceNumber'] = $this->convertToEnglish($input['traceNumber']);
            }
        }

        if (isset($input['transactionID'])) {
            $input['transactionID'] = preg_replace('/\s+/', '', $input['transactionID']);
            if (strlen($input['transactionID']) > 0) {
                $input['transactionID'] = $this->convertToEnglish($input['transactionID']);
            }
        }

        if (isset($input['authority'])) {
            $input['authority'] = preg_replace('/\s+/', '', $input['authority']);
            if (strlen($input['authority']) > 0) {
                $input['authority'] = $this->convertToEnglish($input['authority']);
            }
        }

        if (isset($input['paycheckNumber']) && strlen($input['paycheckNumber']) > 0) {
            $input['paycheckNumber'] = $this->convertToEnglish($input['paycheckNumber']);
        }
        if (isset($input['managerComment']) && strlen($input['managerComment']) > 0) {
            $input['managerComment'] = $this->convertToEnglish($input['managerComment']);
        }
        $this->replace($input);
    }
}
