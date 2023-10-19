<?php

namespace App\PaymentModule\Wallet\Models;

use App\Models\BaseModel;
use App\Models\BaseModel;
use App\Models\Transaction;
use App\Models\User;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Wallet
 *
 * @property int $id
 * @property int|null $user_id           آیدی مشخص کننده کاربر
 *           صاحب کیف پول
 * @property int|null $wallettype_id     آیدی مشخص کننده نوع
 *           کیف
 *           پول
 * @property int $balance           اعتبار کیف پول
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Transaction[] $transactions
 * @property-read User|null $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Wallet onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|\App\Wallet whereBalance($value)
 * @method static Builder|\App\Wallet whereCreatedAt($value)
 * @method static Builder|\App\Wallet whereDeletedAt($value)
 * @method static Builder|\App\Wallet whereId($value)
 * @method static Builder|\App\Wallet whereUpdatedAt($value)
 * @method static Builder|\App\Wallet whereUserId($value)
 * @method static Builder|\App\Wallet whereWallettypeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Wallet withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Wallet withoutTrashed()
 * @mixin Eloquent
 * @method static Builder|\App\Wallet newModelQuery()
 * @method static Builder|\App\Wallet newQuery()
 * @method static Builder|\App\Wallet query()
 * @method static Builder|BaseModel disableCache()
 * @method static Builder|BaseModel withCacheCooldownSeconds($seconds)
 * @property-read \App\Wallettype|null $walletType
 * @property-read mixed $cache_cooldown_seconds
 * @property int $pending_to_reduce مبلغی که علی الحساب از
 *           کیف پول کم شده است
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PaymentModule\Wallet\Wallet
 *         wherePendingToReduce($value)
 * @property-read string|null $cache_clear_url
 * @property-read mixed $date_time
 */
class Wallet extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'wallettype_id',
        'balance',
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

    public function withdraw($amount, $orderId = null)
    {
        if ($amount <= 0) {
            return false;
        }

        if (!($this->hasEnoughBalance($amount))) {
            return false;
        }

        $this->balance = $this->balance - $amount;

        if (!$this->update()) {
            return false;
        }

        $this->transactions()
            ->create([
                'order_id' => $orderId,
                'wallet_id' => $this->id,
                'cost' => $amount,
                'transactionstatus_id' => config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'paymentmethod_id' => config('constants.PAYMENT_METHOD_WALLET'),
                'completed_at' => Carbon::now('Asia/Tehran'),
            ]);

        return true;
    }

    /**
     * Determine if the user can withdraw from this wallet
     *
     * @param  integer  $amount
     *
     * @return boolean
     */
    private function hasEnoughBalance($amount)
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
     * Attempt to add credits to this wallet
     *
     * @param  integer  $amount
     * @param  null  $orderId
     *
     * @return array
     */
    public function withdrawAll($amount, $orderId = null)
    {

    }

    /**
     * Attempt to move credits from this wallet
     *
     * @param  integer  $amount
     * @param  bool  $withoutTransaction
     *
     * @return array
     */
    public function deposit(int $amount, bool $withoutTransaction = false): bool
    {
        $newBalance = $this->balance + $amount;
        $this->balance = $newBalance;
        $result = $this->update();

        if (!$result) {
            return false;
        }

        if ($amount <= 0 || $withoutTransaction) {
            return true;
        }

        $this->transactions()
            ->create([
                'wallet_id' => $this->id,
                'cost' => -$amount,
                'transactionstatus_id' => config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                'completed_at' => Carbon::now('Asia/Tehran'),
            ]);

        return true;
    }

    public function walletType()
    {
        return $this->belongsTo(Wallettype::class, 'wallettype_id', 'id');
    }

    /**
     * @return array
     */
    private function respondFail($msg): array
    {
        return $this->response($msg, false);
    }

    /**
     * @param        $msg
     * @param  bool  $status
     *
     * @return array
     */
    private function response($msg, $status = true): array
    {
        return [
            'result' => $status,
            'responseText' => $msg,
        ];
    }

    /**
     * @return array
     */
    private function respondSuccess($msg): array
    {
        return $this->response($msg);
    }
}
