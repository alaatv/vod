<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Traits\CharacterCommon;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class InsertOrderRequest
 * @package App\Http\Requests
 * @mixin Order
 */
class InsertOrderRequest extends FormRequest
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
        //  For this reason, I commented it for future reviews. عماد نعیمی فر
//        $this->id = $_REQUEST["id"];

        $estimatedCreationDateTime = Carbon::now()->format('Y-m-d H:i:s');
        $transactionMaxStr = $request->transactionstatus_id != config('constants.TRANSACTION_STATUS_SUCCESSFUL') ? '|max:0' : '';
        return [
            'user_id' => 'nullable|integer|min:1|exists:users,id',
//            'insertor_id' => 'nullable|integer|min:1|exists:users,id',
            'orderstatus_id' => 'nullable|integer|min:1|exists:orderstatuses,id',
            'paymentstatus_id' => 'nullable|integer|min:1|exists:paymentstatuses,id',
            'coupon_id' => 'nullable|integer|min:1|exists:coupons,id',
            'couponDiscount' => 'sometimes|numeric|min:0|max:100',
            'couponDiscountAmount' => 'sometimes|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'costwithoutcoupon' => 'nullable|numeric|min:0|gte:cost',
            'discount' => 'sometimes|numeric|min:0',
            'customerDescription' => 'nullable|string|min:2',
            'customerExtraInfo' => 'nullable|string|min:2',
            // Obviously, the checkout time and completion time shouldn't be before than the order creation time.
            'checkOutDateTime' => "nullable|date_format:Y-m-d H:i:s|after_or_equal:{$estimatedCreationDateTime}",
            'completed_at' => "nullable|date_format:Y-m-d H:i:s|after_or_equal:{$estimatedCreationDateTime}",
            'transactionstatus_id' => 'sometimes|integer|min:1|exists:transactionstatuses,id',
            'transactionID' => 'sometimes|integer|min:0'.$transactionMaxStr,
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
