<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\TransactionSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditTransactionRequest;
use App\Http\Requests\InsertTransactionRequest;
use App\Http\Requests\Request;
use App\Http\Resources\Admin\TransactionResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Order;
use App\Models\Orderproduct;
use App\Models\Transaction;
use App\Repositories\TransactionRepo;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Shetabit\Multipay\Drivers\Zarinpal\Zarinpal;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * Class TransactionController.
 * For Api Version 2.
 * For Admin side.
 *
 * @package App\Http\Controllers\Api\Admin
 */
class TransactionController extends Controller
{
    /**
     * Return a listing of the resource.
     *
     * @param  TransactionSearch  $transactionSearch
     * @return ResourceCollection
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(TransactionSearch $transactionSearch)
    {
        // Set the number of items on each page.
        if (request()->has('length') && request()->length > 0) {
            $transactionSearch->setNumberOfItemInEachPage(request()->get('length'));
        }

        // Filter resources based on received parameters.
        $transactionResult = $transactionSearch->get(request()->all());

        return TransactionResource::collection($transactionResult);
    }

    /**
     * Return the specified resource.
     *
     * @param  Transaction  $transaction
     * @return TransactionResource
     */
    public function show(Transaction $transaction)
    {
        return (new TransactionResource($transaction));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertTransactionRequest  $request
     * @return JsonResponse
     */
    public function store(InsertTransactionRequest $request)
    {
        $transaction = new Transaction();

        $this->fillTransactionFromRequest($request->all(), $transaction);

        try {
            $transaction->save();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new TransactionResource($transaction->refresh()))->response();
    }

    /**
     * Fill the model object to be stored or updated in database.
     *
     * @param  array  $inputData
     * @param  Transaction  $transaction
     */
    private function fillTransactionFromRequest(array $inputData, Transaction $transaction): void
    {
        $transaction->fill($inputData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditTransactionRequest  $request
     * @param  Transaction  $transaction
     * @return JsonResponse
     */
    public function update(EditTransactionRequest $request, Transaction $transaction)
    {
        $this->fillTransactionFromRequest($request->all(), $transaction);

        try {
            $transaction->update($request->all());
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return (new TransactionResource($transaction->refresh()))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Transaction  $transaction
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json();
    }

    public function convertToDonate(Transaction $transaction)
    {
        if ($transaction->cost >= 0 || isset($transaction->traceNumber)) {
            return response()->json(['message' => 'این تراکنش بازگشت هزینه نمی باشد'],
                ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
        }

        $order = Order::FindOrFail($transaction->order->id);
        $donateOrderproduct = new Orderproduct();
        $donateOrderproduct->order_id = $order->id;
        $donateOrderproduct->product_id = 182;
        $donateOrderproduct->cost = -$transaction->cost;

        if (!$donateOrderproduct->save()) {
            return response()->json(['message' => 'خطا در ایجاد آیتم کمک مالی . لطفا دوباره اقدام نمایید.'],
                ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
        }

        if (!$transaction->forceDelete()) {
            return response()->json(['message' => 'خطا در بروز رسانی تراکنش . لطفا تراکنش را دستی اصلاح نمایید.'],
                ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
        }

        $newOrder = Order::where('id', $order->id)->get()->first();
        $orderCostArray = $newOrder->obtainOrderCost(true, false, 'REOBTAIN');
        $newOrder->cost = $orderCostArray['rawCostWithDiscount'];
        $newOrder->costwithoutcoupon = $orderCostArray['rawCostWithoutDiscount'];
        if ($newOrder->update()) {
            return response()->json(['message' => 'عملیات تبدیل با موفقیت انجام شد.']);
        }
        return response()->json(['message' => 'خطا در بروز رسانی سفارش . لطفا سفارش را دستی اصلاح نمایید.'],
            ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }

    public function completeTransaction(Request $request, Transaction $transaction)
    {
        if (isset($transaction->traceNumber)) {
            return null;
        }

        $transaction->traceNumber = $request->get('traceNumber');
        $transaction->paymentmethod_id = config('constants.PAYMENT_METHOD_ATM');
        $transaction->managerComment =
            $transaction->managerComment.'شماره کارت مقصد: \n'.$request->get('managerComment');
        if ($transaction->update()) {
            return response()->json(['message' => 'اطلاعات تراکنش با موفقیت ذخیره شد']);
        }
        return response()->json(['message' => 'خطا در ذخیره اطلاعات . لفطا مجددا اقدام نمایید'],
            ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }

    public function limitedUpdate(Request $request, Transaction $transaction)
    {
        $order = Order::FindOrFail($request->get('order_id'));
        if (!$this->checkOrderAuthority($order)) {
            return response()->json(['message' => 'Order not found.'], ResponseAlias::HTTP_NOT_FOUND);
        }
        if ($order->id != $transaction->order_id) {
            return response()->json(['message' => 'Order ID mismatch.'], ResponseAlias::HTTP_NOT_FOUND);
        }

        $editRequest = new EditTransactionRequest();

        $paymentImplied = false;
        if ($request->has('referenceNumber')) {
            $editRequest->offsetSet('referenceNumber', $request->get('referenceNumber'));
            $paymentImplied = true;
        }
        if ($request->has('traceNumber')) {
            $editRequest->offsetSet('traceNumber', $request->get('traceNumber'));
            $paymentImplied = true;
        }

        if ($request->has('paymentmethod_id')) {
            $editRequest->offsetSet('paymentmethod_id', $request->get('paymentmethod_id'));
        }

        if (!$paymentImplied) {
            return response()->json(['message' => 'No payment data provided.'], ResponseAlias::HTTP_BAD_REQUEST);
        }
        $editRequest->offsetSet('transactionstatus_id', config('constants.TRANSACTION_STATUS_PENDING'));
        $editRequest->offsetSet('completed_at', Carbon::now());
        $editRequest->offsetSet('apirequest', true);
        $response = $this->update($editRequest, $transaction);
        if ($response->getStatusCode() == ResponseAlias::HTTP_OK) {
            return response()->json(['message' => 'تراکنش با موفقیت ثبت شد'], ResponseAlias::HTTP_OK);
        } elseif ($response->getStatusCode() == ResponseAlias::HTTP_SERVICE_UNAVAILABLE) {
            return response()->json(['message' => 'خطای پایگاه داده ، لطفا مجددا اقدام نمایید.'],
                ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
        } else {
            return response()->json(['message' => 'خطای نا مشخص'], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUnverifiedTransactions()
    {
        try {
            $merchant = config('Zarinpal.merchantID');
#TODO:Need to check
            $zarinPal = new Zarinpal($merchant);
            $result = $zarinPal->getDriver()->unverifiedTransactions(['MerchantID' => $merchant]);

            $error = null;
            $transactions = collect();
            if ($result['Status'] == 'success') {
                $authorities = $result['Authorities'];
                if (is_null($authorities)) {
                    $authorities = collect();
                }

                foreach ($authorities as $authority) {
                    $transaction = TransactionRepo::getTransactionByAuthority($authority['Authority'])->getValue(null);
                    $userId = null;
                    $firstName = '';
                    $lastName = '';
                    $mobile = '';
                    $jalaliCreatedAt = '';
                    if (!is_null($transaction)) {
                        $jalaliCreatedAt = $transaction->jalali_created_at;
                        $user = optional($transaction->order)->user;
                        if (isset($user)) {
                            $userId = $user->id;
                            $firstName = $user->firstName;
                            $lastName = $user->lastName;
                            $mobile = $user->mobile;
                        }
                    }

                    $transactions->push([
                        'userId' => $userId,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'mobile' => $mobile,
                        'authority' => $authority['Authority'],
                        'amount' => $authority['Amount'],
                        'created_at' => $jalaliCreatedAt,
                        'transactionId' => optional($transaction)->id,
                        'orderId' => optional(optional($transaction)->order)->id,
                    ]);
                }
            } else {
                Log::error(json_encode(['result' => $result, 'error' => $result['error']], JSON_UNESCAPED_UNICODE));
                $error = $result['error'];
            }

            return response()->json(compact('transactions', 'error'), ResponseAlias::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Unexpected error',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
