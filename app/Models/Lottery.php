<?php

namespace App\Models;

use App\Traits\DateTrait;
use Carbon\Carbon;

class Lottery extends BaseModel
{
    use DateTrait;

    protected $fillable = [
        'name',
        'displayName',
        'holdingDate',
        'essentialPoints',
        'prizes',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('rank', 'prizes');
    }

    public function prizes($rank)
    {
        $prizeName = '';
        $amount = 0;
        $memorial = '';
        if ($this->id == 13 && $rank == 1) {//nafare aval
            $prizeName = 'مبلغ '.number_format(500000).' تومان جایزه نقدی';

//            else {
//                $memorial = "کد تخفیف ayft با 70 درصد تخفیف";
//            }
//            elseif($rank <= 11 ){
//                $prizeName = "مبلغ ".number_format(300000). " تومان جایزه نقدی";
//            }
        }

        return [
            $prizeName,
            $amount,
            $memorial,
        ];
    }

    public function getHoldingDateAttribute($value)
    {
        return $this->getDateTimeAttribute($value);
    }

    public function getHoldigDateLocalAttribute($value)
    {
        return Carbon::parse($this->attributes['holdingDate'])->format('Y-m-d\TH:i:s');
    }

    public function status()
    {
        return $this->belongsTo(LotteryStatus::class, 'lottery_status_id');
    }
}





