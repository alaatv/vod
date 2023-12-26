<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Traits\CharacterCommon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EditOrderRequest
 *
 * @mixin Order
 */
class EditOrderRequest extends FormRequest
{
    use CharacterCommon;

    protected $id;

    public function authorize()
    {
        return true;
    }

    public function rules(\Illuminate\Http\Request $request)
    {
        // TODO: The following code doesn't appear to be needed. On the other hand, the following code has encountered an error.
        //  For this reason, I commented it for future reviews.
        //        $this->id = $_REQUEST["id"];

        $transactionMaxStr = isset($request->transactionstatus_id) && $request->transactionstatus_id != config('constants.TRANSACTION_STATUS_SUCCESSFUL') ? '|max:0' : '';

        return [
            // Order table fields
            'user_id' => 'nullable|integer|min:1|exists:users,id,deleted_at,NULL',
            'orderstatus_id' => 'nullable|integer|min:1|exists:orderstatuses,id,deleted_at,NULL',
            'paymentstatus_id' => 'nullable|integer|min:1|exists:paymentstatuses,id,deleted_at,NULL',
            //   'coupon_id' => 'nullable|integer|min:1|exists:coupons,id,deleted_at,NULL',
            'discount' => 'nullable|integer|min:0',
            'transactionstatus_id' => 'sometimes|integer|min:1|exists:transactionstatuses,id,deleted_at,NULL',
            'transactionID' => 'sometimes|integer|min:0'.$transactionMaxStr,
            // Another input fields
            'managerDescription' => 'nullable|string|min:2',
            'orderstatusSMS' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:jpeg,jpg,png,bmp,tiff|max:4096',
            'updateOrderProductsTmpCost' => 'nullable|boolean',
            'updateOrderProductsShareCost' => 'nullable|boolean',
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
        if (isset($input['discount'])) {
            $input['discount'] = $this->convertToEnglish($input['discount']);
        }
        $this->replace($input);
    }
}
