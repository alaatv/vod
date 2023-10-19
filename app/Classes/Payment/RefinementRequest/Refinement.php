<?php
/**
 * Created by PhpStorm.
 * User: Alaaa
 * Date: 1/8/2019
 * Time: 1:17 PM
 */

namespace App\Classes\Payment\RefinementRequest;

use App\Http\Controllers\Web\TransactionController;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\OrderCommon;
use Illuminate\Http\Response;

abstract class Refinement
{
    use OrderCommon;

    /**
     * @var array $inputData
     */
    protected $inputData;

    /**
     * @var int $statusCode
     */
    protected $statusCode;

    /**
     * @var string $message
     */
    protected $message;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var Order $order
     */
    protected $order;

    /**
     * @var Order $order
     */
    protected $orderUniqueId;

    /**
     * @var int $cost
     */
    protected $cost;

    /**
     * @var int $paidFromWalletCost
     */
    protected $paidFromWalletCost;

    /**
     * @var int $donateCost
     */
    protected $donateCost;

    /**
     * @var Transaction $transaction
     */
    protected $transaction;

    /**
     * @var string $description
     */
    protected $description;

    /**
     * @var int $walletId
     */
    protected $walletId;

    /**
     * @var int $walletChargingAmount
     */
    protected $walletChargingAmount;

    /**
     * @var TransactionController
     */
    protected $transactionController;

    public function __construct()
    {
        $this->donateCost = 0;
        $this->statusCode = Response::HTTP_OK;
        $this->message = '';
        $this->description = '';
    }

    /**
     * @param  array  $inputData
     *
     * @return Refinement
     */
    public function setData(array $inputData): Refinement
    {
        $this->inputData = $inputData;
        $this->transactionController = $this->inputData['transactionController'] ?? null;
        $this->user = $this->inputData['user'] ?? null;
        $this->walletId = $this->inputData['walletId'] ?? null;
        $this->walletChargingAmount = $this->inputData['walletChargingAmount'] ?? null;

        return $this;
    }

    /**
     * @return Refinement
     */
    public function validateData(): Refinement
    {
        if (!isset($this->user)) {
            $this->message = 'user not set';
            $this->statusCode = Response::HTTP_BAD_REQUEST;
        }
        if (!isset($this->transactionController)) {
            $this->message = 'transactionController not set';
            $this->statusCode = Response::HTTP_BAD_REQUEST;
        }

        return $this;
    }

    /**
     * @return Refinement
     */
    public abstract function loadData(): Refinement;

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            'statusCode' => $this->statusCode,
            'message' => $this->message,
            'user' => $this->user,
            'order' => $this->order,
            'orderUniqueId' => $this->orderUniqueId,
            'cost' => $this->cost,
            'donateCost' => $this->donateCost,
            'transaction' => $this->transaction,
            'description' => $this->description,
        ];
    }

    protected function getOrderCost(): void
    {
        $this->validateCoupon();
        $this->order->refreshCost();
        $this->cost = $this->order->totalCost() - $this->order->totalPaidCost();
    }

    protected function validateCoupon(): void
    {
        $coupon = $this->order->coupon;
        $couponValidationStatus = optional($coupon)->validateCoupon();
        if (!($couponValidationStatus != Coupon::COUPON_VALIDATION_STATUS_OK && $couponValidationStatus != Coupon::COUPON_VALIDATION_STATUS_USAGE_LIMIT_FINISHED)) {
            return;
        }
        $this->order->detachCoupon();
        if ($this->order->updateWithoutTimestamp()) {
            $coupon->decreaseUseNumber();
            $coupon->update();
        }
        $this->order->fresh();

    }

    /**
     * @param  bool  $deposit
     *
     * @return array
     */
    protected function getNewTransaction($deposit = true)
    {
        if ($deposit) {
            $data['cost'] = $this->cost;
        } else {
            $data['cost'] = ($this->cost * (-1));
        }

        $data['description'] = $this->description;
        $data['order_id'] = (isset($this->order)) ? $this->order->id : null;
        $data['wallet_id'] = (isset($this->walletId)) ? $this->walletId : null;
        $data['destinationBankAccount_id'] = 1; // ToDo: Hard Code
        $data['paymentmethod_id'] = config('constants.PAYMENT_METHOD_ONLINE');
        $data['transactionstatus_id'] = config('constants.TRANSACTION_STATUS_TRANSFERRED_TO_PAY');
        $result = $this->transactionController->new($data);

        return $result;
    }

    /**
     * @return bool
     */
    protected function canDeductFromWallet(?int $seller)
    {
        if ($seller == config('constants.SOALAA_SELLER')) {
            return false;
        }
        return true;
    }

    protected function payByWallet(): void
    {
        $deductibleCostFromWallet = $this->cost - $this->donateCost;
        $remainedCost = $deductibleCostFromWallet;
        if ($deductibleCostFromWallet == 0) {
            $this->paidFromWalletCost = 0;
            return;
        }

        $walletPayResult = $this->canPayOrderByWallet($this->user, $deductibleCostFromWallet);
        if ($walletPayResult['result']) {
            $remainedCost = $walletPayResult['cost'];
        }
        $remainedCost = $remainedCost + $this->donateCost;
        $this->cost = $remainedCost;
        $this->paidFromWalletCost = $deductibleCostFromWallet - $remainedCost;
    }


    protected function resetWalletPendingCredit(): void
    {
        $wallets =
            $this->user->wallets->sortByDesc('wallettype_id'); //Chon mikhastim aval az kife poole hedie kam shavad!

        /** @var Wallet $wallet */
        foreach ($wallets as $wallet) {
            $wallet->update([
                'pending_to_reduce' => 0,
            ]);
        }
    }
}
