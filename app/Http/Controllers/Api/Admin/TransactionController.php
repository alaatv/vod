<?php

namespace App\Http\Controllers\Api\Admin;

use App\Classes\Search\TransactionSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditTransactionRequest;
use App\Http\Requests\InsertTransactionRequest;
use App\Http\Resources\Admin\TransactionResource;
use App\Http\Resources\ResourceCollection;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;

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
     * @return JsonResponse|TransactionResource|RedirectResponse|Redirector
     */
    public function show(Transaction $transaction)
    {
        return (new TransactionResource($transaction));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertTransactionRequest|Transaction  $request
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
     * @param  EditTransactionRequest|Transaction  $request
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
     * @return Exception|JsonResponse
     * @throws Exception
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'خطای پایگاه داده', 'errorInfo' => $e],
                Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json();
    }
}
