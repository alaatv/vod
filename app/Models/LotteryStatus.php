<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class LotteryStatus extends Model
{
    public const INACTIVE = 1;
    public const SCORING = 2;
    public const WAIT_FOR_SCORING = 3;
    public const REPORT_SCORING_ERROR = 4;
    public const HOLD = 5;
    public const WAIT_FOR_HOLDING_LOTTERY = 6;
    public const REPORT_HOLDING_ERROR = 7;
    public const HOLDED = 8;

    public const SCORING_ACTION = 'scoring';
    public const WAIT_FOR_SCORING_ACTION = 'waitForScoring';
    public const SCORING_ERROR_ACTION = 'reportScoringError';
    public const HOLD_ACTION = 'holdLottery';
    public const WAIT_FOR_HOLDING_ACTION = 'waitForHoldingLottery';
    public const HOLDING_ERROR_ACTION = 'reportScoringError';
    public const REPORT_HOLDING_ERROR_ACTION = 'reportHoldingError';
    public const HOLDED_ACTION = 'holded';

    protected $table = 'lottery_status';

    public function lotteries()
    {
        return $this->hasMany(Lottery::class);
    }
}
