<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    use HasFactory;

    public const REFERRAL_CODE_VALIDATION_STATUS_OK = 0;
    public const REFERRAL_CODE_VALIDATION_STATUS_DISABLED = 1;
    public const REFERRAL_CODE_VALIDATION_STATUS_USAGE_LIMIT_FINISHED = 2;


    //region properties
    public const REFERRAL_CODE_VALIDATION_INTERPRETER = [
        self::REFERRAL_CODE_VALIDATION_STATUS_DISABLED => 'کارت هدیه غیر فعال است',
        self::REFERRAL_CODE_VALIDATION_STATUS_USAGE_LIMIT_FINISHED => 'ظرفیت استفاده از کارت هدیه به پایان رسیده است',
    ];
    /**
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'event_id',
        'referralRequest_id',
        'code',
        'enable',
        'usageNumber',
        'isAssigned',
        'assignor_id',
        'assignor_device_id',
        'used_at',
    ];
    protected $casts = [
        'enable' => 'boolean',
        'used_at' => 'datetime',
    ];
    protected $table = 'referral_codes';
    //endregion


    //region relations

    /**
     * Get the owner of the code.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event that contains the code.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function users()
    {
        return $this->hasMany(ReferralCodeUser::class, 'code_id', 'id');
    }

    public function referralCommissions()
    {
        return $this->hasMany(ReferralCodeCommission::class, 'referralRequest_id');
    }

    public function referralRequest()
    {
        return $this->belongsTo(ReferralRequest::class, 'referralRequest_id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class, 'referralCode_id');
    }

    //endregion
//region other
    public function decreaseUseNumber(): self
    {
        $this->usageNumber = max($this->usageNumber - 1, 0);
        return $this;
    }

    public function increaseUseNumber(): self
    {
        $this->usageNumber++;
        return $this;
    }
//endregion

//region referralCodeValidation
    /**
     * Validates a referral code
     *
     * @return int
     */
    public function validateReferralCode()
    {

        if ($this->hasTotalNumberFinished()) {
            return self::REFERRAL_CODE_VALIDATION_STATUS_USAGE_LIMIT_FINISHED;
        }
        if (!$this->isEnable()) {
            return self::REFERRAL_CODE_VALIDATION_STATUS_DISABLED;
        }

        return self::REFERRAL_CODE_VALIDATION_STATUS_OK;
    }

    /**
     * Determines whether this referral code total number has finished or not
     *
     * @return bool
     */
    public function hasTotalNumberFinished(): bool
    {
        return isset($this->referralRequest->usageLimit) && $this->usageNumber >= $this->referralRequest->usageLimit;
    }

    /**
     * Determines whether this coupon is enabled or not
     *
     * @return bool
     */
    public function isEnable(): bool
    {
        return (bool) $this->enable;
    }

//endregion

    public function scopeAssigned(Builder $query, int $isAssigned): Builder
    {
        return $query->where('isAssigned', $isAssigned);
    }

    public function scopeUsed(Builder $query, int $isUsed): Builder
    {
        if ($isUsed == 0) {
            return $query->where('usageNumber', 0);
        }
        return $query->where('usageNumber', '>=', 1);
    }

    public function scopeSold(Builder $query)
    {
        return $query->whereHas('orders', function ($query) {
            $query->paidAndClosed();
        });
    }

    public function scopeNotSold(Builder $query)
    {
        return $query->whereDoesntHave('orders', function ($query) {
            $query->paidAndClosed();
        });
    }
}
