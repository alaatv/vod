<?php

namespace App\Models;

use App\Collection\TransactionCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Transaction extends BaseModel
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'wallet_id',
        'cost',
        'authority',
        'transactionID',
        'traceNumber',
        'referenceNumber',
        'paycheckNumber',
        'managerComment',
        'sourceBankAccount_id',
        'destinationBankAccount_id',
        'paymentmethod_id',
        'transactiongateway_id',
        'transactionstatus_id',
        'completed_at',
        'deadline_at',
        'description',
        'device_id',
        'gateway_status',
        'gateway_token',
    ];

    protected $appends = [
        'paymentmethod',
        'transactiongateway',
        'jalaliCompletedAt',
        'jalaliDeadlineAt',
    ];

    protected $hidden = [
        'order_id',
        'destinationBankAccount_id',
        'paymentmethod_id',
        'transactiongateway_id',
        'updated_at',
    ];

    /**
     * Create a new Eloquent Collection instance.
     *
     *
     * @return TransactionCollection
     */
    public function newCollection(array $models = [])
    {
        return new TransactionCollection($models);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function transactionstatus()
    {
        return $this->belongsTo(Transactionstatus::class);
    }

    public function sourceBankAccount()
    {
        return $this->belongsTo(Bankaccount::class, 'bankaccounts', 'sourceBankAccount_id', 'id');
    }

    public function destinationBankAccount()
    {
        return $this->belongsTo(Bankaccount::class, 'destinationBankAccount_id');
    }

    public function commissions()
    {
        return $this->hasMany(UserCommission::class);
    }

    /**
     * @return BelongsTo
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * @return BelongsTo
     */
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * @return string
     * Converting Created_at field to jalali
     */
    public function DeadlineAt_Jalali()
    {
        /*$explodedDateTime = explode(" ", $this->deadline_at);*/
        //        $explodedTime = $explodedDateTime[1] ;
        return $this->convertDate($this->deadline_at, 'toJalali');
    }

    public function parents()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_transaction', 't2_id',
            't1_id')
            ->withPivot('relationtype_id')
            ->join('transactioninterraltions', 'relationtype_id',
                'transactioninterraltions.id')//            ->select('major1_id AS id', 'majorinterrelationtypes.name AS pivot_relationName' , 'majorinterrelationtypes.displayName AS pivot_relationDisplayName')
            ->where('relationtype_id', config('constants.TRANSACTION_INTERRELATION_PARENT_CHILD'));
    }

    public function children()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_transaction', 't1_id',
            't2_id')
            ->withPivot('relationtype_id')
            ->join('transactioninterraltions', 'relationtype_id', 'contenttypeinterraltions.id')
            ->where('relationtype_id',
                config('constants.TRANSACTION_INTERRELATION_PARENT_CHILD'));
    }

    public function getGrandParent()
    {
        $counter = 1;
        $parentsArray = [];
        $myTransaction = $this;
        while ($myTransaction->hasParents()) {
            $parentsArray = Arr::add($parentsArray, $counter++, $myTransaction->parents->first());
            $myTransaction = $myTransaction->parents->first();
        }
        if (empty($parentsArray)) {
            return false;
        }

        return Arr::last($parentsArray);
    }

    public function hasParents($depth = 1)
    {
        $counter = 0;
        $myTransaction = $this;
        while (! $myTransaction->parents->isEmpty()) {
            if ($counter >= $depth) {
                break;
            }
            $myTransaction = $myTransaction->parents->first();
            $counter++;
        }
        if ($myTransaction->id == $this->id || $counter != $depth) {
            return false;
        }

        return true;
    }

    public function getCode()
    {
        if (isset($this->transactionID)) {
            return 'شماره تراکنش: '.$this->transactionID;
        }
        if (isset($this->traceNumber)) {
            return 'شماره پیگیری: '.$this->traceNumber;
        }
        if (isset($this->referenceNumber)) {
            return 'شماره مرجع: '.$this->referenceNumber;
        }
        if (isset($this->paycheckNumber)) {
            return 'شماره چک: '.$this->paycheckNumber;
        }

        return false;
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeAuthority($query, string $authority)
    {
        return $query->where('authority', $authority);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeWalletMethod($query)
    {
        return $query->where('paymentmethod_id', config('constants.PAYMENT_METHOD_WALLET'));
    }

    public function scopeOnlineMethod($query)
    {
        return $query->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'));
    }

    public function scopeDesktopDevice($query)
    {
        return $query->where('device_id', config('constants.DEVICE_TYPE_DESKTOP'));
    }

    public function scopeAndroidDevice($query)
    {
        return $query->where('device_id', config('constants.DEVICE_TYPE_ANDROID'));
    }

    public function scopeSettlement($query, $bankAccountId = null)
    {
        $bankAccountId ? $query->where('destinationBankAccount_id',
            $bankAccountId) : $query->whereNotNull('destinationBankAccount_id');

        return $query->whereNotNull('wallet_id')->whereNull('order_id');
    }

    public function isSettlement()
    {
        return $this->whereNotNull('destinationBankAccount_id')
            ->whereNotNull('wallet_id')->whereNull('order_id')
            ->count();
    }

    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'));
    }

    public function depositThisWalletTransaction()
    {
        $wallet = $this->wallet;
        $amount = $this->cost;
        if (isset($wallet)) {
            $response = $wallet->deposit($amount);
        } else {
            $response = ['result' => false];
        }

        return $response;
    }

    public function getTransactionGatewayAttribute()
    {
        return optional($this->transactiongateway()
            ->first())->setVisible([
                'name',
                'displayName',
                'description',
            ]);
    }

    public function transactiongateway()
    {
        return $this->belongsTo(Transactiongateway::class);
    }

    public function getPaymentmethodAttribute()
    {
        return optional($this->paymentmethod()
            ->first())->setVisible([
                'name',
                'displayName',
                'description',
            ]);
    }

    public function paymentmethod()
    {
        return $this->belongsTo(Paymentmethod::class);
    }

    public function getJalaliCompletedAtAttribute()
    {
        $transaction = $this;
        $key = 'transaction:jalaliCompletedAt:'.$transaction->cacheKey();

        return Cache::tags(['transaction'])
            ->remember($key, config('constants.CACHE_600'), function () use ($transaction) {
                if (isset($transaction->completed_at)) {
                    return $this->convertDate($transaction->completed_at, 'toJalali');
                }

                return null;
            });
    }

    public function getJalaliDeadlineAtAttribute()
    {
        $transaction = $this;
        $key = 'transaction:jalaliDeadlineAt:'.$transaction->cacheKey();

        return Cache::tags(['transaction'])
            ->remember($key, config('constants.CACHE_600'), function () use ($transaction) {
                if (isset($transaction->deadline_at)) {
                    return $this->convertDate($transaction->deadline_at, 'toJalali');
                }

                return null;
            });
    }

    public function getJalaliCreatedAtAttribute()
    {
        $transaction = $this;
        $key = 'transaction:jalaliCreatedAt:'.$transaction->cacheKey();

        return Cache::tags(['transaction'])
            ->remember($key, config('constants.CACHE_600'), function () use ($transaction) {
                if (isset($transaction->created_at)) {
                    return $this->convertDate($transaction->created_at, 'toJalali');
                }

                return null;
            });
    }

    public function getOwnerFullNameAttribute()
    {
        return $this->destinationBankAccount?->user?->full_name;
    }
}
