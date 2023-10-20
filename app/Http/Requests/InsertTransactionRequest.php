<?php

namespace App\Http\Requests;

use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

class InsertTransactionRequest extends FormRequest
{
    use CharacterCommon;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id' => 'required|integer|min:1|exists:orders,id',
            'wallet_id' => 'nullable|integer|min:1|exists:wallets,id',
            'cost' => 'required|integer',
            // The authority and transactionID fields in database are unique. So we can't consider deleted_at for those.
            'authority' => 'nullable|string|min:2|max:255|unique:transactions,authority',
            'transactionID' => 'nullable|string|min:1|max:255|unique:transactions,transactionID',
            'traceNumber' => 'nullable|numeric|unique:transactions,traceNumber,NULL,id,deleted_at,NULL',
            'referenceNumber' => 'nullable|numeric|unique:transactions,referenceNumber,NULL,id,deleted_at,NULL',
            'paycheckNumber' => 'nullable|string|min:2|max:255|unique:transactions,paycheckNumber,NULL,id,deleted_at,NULL',
            'managerComment' => 'nullable|string|min:2|max:255',
            'sourceBankAccount_id' => 'nullable|integer|min:1|exists:bankaccounts,id',
            'destinationBankAccount_id' => 'nullable|integer|min:1|exists:bankaccounts,id',
            'paymentmethod_id' => 'nullable|integer|min:1|exists:paymentmethods,id',
            'device_id' => 'nullable|integer|min:1|exists:devices,id',
            'transactiongateway_id' => 'nullable|integer|min:1|exists:transactiongateways,id',
            'transactionstatus_id' => 'required|integer|min:1|exists:transactionstatuses,id',
            'description' => 'nullable|string|min:2',
//            'deadline_at' => 'sometimes|date_format:Y-m-d H:i:s',
//            'completed_at' => 'sometimes|date_format:Y-m-d H:i:s',
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
        if (isset($input['cost'])) {
            $input['cost'] = preg_replace('/\s+/', '', $input['cost']);
            $input['cost'] = $this->convertToEnglish($input['cost']);
        }
        if (isset($input['referenceNumber'])) {
            $input['referenceNumber'] = preg_replace('/\s+/', '', $input['referenceNumber']);
            $input['referenceNumber'] = $this->convertToEnglish($input['referenceNumber']);
        }
        if (isset($input['traceNumber'])) {
            $input['traceNumber'] = preg_replace('/\s+/', '', $input['traceNumber']);
            $input['traceNumber'] = $this->convertToEnglish($input['traceNumber']);
        }
        if (isset($input['transactionID'])) {
            $input['transactionID'] = preg_replace('/\s+/', '', $input['transactionID']);
            $input['transactionID'] = $this->convertToEnglish($input['transactionID']);
        }
        if (isset($input['authority'])) {
            $input['authority'] = preg_replace('/\s+/', '', $input['authority']);
            $input['authority'] = $this->convertToEnglish($input['authority']);
        }
        if (isset($input['paycheckNumber'])) {
            $input['paycheckNumber'] = preg_replace('/\s+/', '', $input['paycheckNumber']);
            $input['paycheckNumber'] = $this->convertToEnglish($input['paycheckNumber']);
        }
        if (isset($input['managerComment']) && strlen($input['managerComment']) > 0) {
            $input['managerComment'] = $this->convertToEnglish($input['managerComment']);
        }
        $this->replace($input);
    }
}
