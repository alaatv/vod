<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends BaseModel
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'wallettype_id',
        'balance',
        'pending_to_reduce',
    ];

    /**
     * Retrieve owner
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Force to move credits from this account
     *
     * @param  integer  $amount
     *
     * @return array
     */
    public function forceWithdraw($amount)
    {
        return $this->withdraw($amount, false);
    }

    /**
     * Attempt to add credits to this wallet
     *
     * @param  integer  $amount
     * @param  null  $orderId
     * @param  boolean  $shouldCheckWithdraw
     *
     * @param  bool  $withCreatingTransaction
     *
     * @return array
     */
    public function withdraw(
        $amount,
        $orderId = null,
        $shouldCheckWithdraw = true,
        $withCreatingTransaction = true,
        $description = null
    ) {
        /**
         * unused variable
         */ /*$failed = true;*/
        /*$responseText = '';*/

        $accepted = $shouldCheckWithdraw ? $this->canWithdraw($amount) : true;

        if ($amount > 0) {
            if ($accepted) {
                $newBalance = $this->balance - $amount;
                $this->balance = $newBalance;
                $result = $this->update();
                if ($result) {
                    $responseText = 'SUCCESSFUL';
                    $failed = false;
                    if ($withCreatingTransaction) {
                        $this->transactions()
                            ->create([
                                'order_id' => $orderId,
                                'wallet_id' => $this->id,
                                'cost' => $amount,
                                'description' => $description,
                                'transactionstatus_id' => config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                                'paymentmethod_id' => config('constants.PAYMENT_METHOD_WALLET'),
                                'completed_at' => Carbon::now('Asia/Tehran'),
                            ]);
                    }
                } else {
                    $failed = true;
                    $responseText = 'CAN_NOT_UPDATE_WALLET';
                }
            } else {
                $failed = true;
                $responseText = 'CAN_NOT_WITHDRAW';
            }

        } else {
            $failed = true;
            $responseText = 'CAN_NOT_WITHDRAW';
        }

        return [
            'result' => !$failed,
            'responseText' => $responseText,
        ];
    }

    /**
     * Determine if the user can withdraw from this wallet
     *
     * @param  integer  $amount
     *
     * @return boolean
     */
    public function canWithdraw($amount)
    {
        return $this->balance >= $amount;
    }

    /**
     * Retrieve all transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Attempt to move credits from this wallet
     *
     * @param  integer  $amount
     * @param  bool  $withoutTransaction
     *
     * @return array
     */
    public function deposit(int $amount, bool $withoutTransaction = false): array
    {
        /**
         * unused variable
         */

        if ($amount > 0) {
            $newBalance = $this->balance + $amount;
            $this->balance = $newBalance;
            $result = $this->update();
            if ($result) {
                if (!$withoutTransaction) {
                    $transactionStatus = config('constants.TRANSACTION_STATUS_SUCCESSFUL');
                    $transaction = $this->transactions()
                        ->create([
                            'wallet_id' => $this->id,
                            'cost' => -$amount,
                            'transactionstatus_id' => $transactionStatus,
                            'completed_at' => Carbon::now('Asia/Tehran'),
                        ]);

                    if (isset($transaction)) {
                        $responseText = 'SUCCESSFUL';
                        $failed = false;
                    } else {
                        $failed = true;
                        $responseText = 'CAN_NOT_UPDATE_WALLET';
                    }
                } else {
                    $responseText = 'SUCCESSFUL';
                    $failed = false;
                }
            } else {
                $failed = true;
                $responseText = 'CAN_NOT_UPDATE_WALLET';
            }
        } else {
            $failed = true;
            $responseText = 'WRONG_AMOUNT';
        }

        return [
            'result' => !$failed,
            'responseText' => $responseText,
        ];
    }

    public function walletType()
    {
        return $this->belongsTo(Wallettype::class, 'wallettype_id', 'id');
    }
}
