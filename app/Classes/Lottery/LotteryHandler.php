<?php


namespace App\Classes\Lottery;


use App\Models\Lottery;
use App\Models\LotteryStatus;
use App\Notifications\HoldingLotteryErrorNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class LotteryHandler implements LotteryHandlerInterface
{
    // TODO: need to define developer id or name for notifications
    public const DEVELOPER = '09302631762';
    private $lottery;
    private $scoringCommandArgument;

    public function __construct(Lottery $lottery, Request $request)
    {
        $this->lottery = $lottery;
        $this->scoringCommandArgument = $request->get('scoreType', 'noType');
    }

    public function scoring()
    {
        try {
            Artisan::queue('alaaTv:giveLotteryPoint2', ['id' => $this->lottery->id]);
            $this->lottery->lottery_status_id = LotteryStatus::WAIT_FOR_SCORING;
            $this->lottery->save();
        } catch (Exception $exception) {
            return 'در حال حاضر امکان امتیازدهی به این قرعه کشی وجود ندارد';
        }
        return 'امتیاز دهی به این قرعه کشی در صف اجرا قرار گرفت. پس از اتمام وضعیت قرعه کشی به روز رسانی خواهد شد.';

    }

    public function waitForScoring()
    {
        return "سیستم امتیاز دهی در حال کار است. لطفا تا اتمام امتیاز دهی صبر بفرمایید. با پایان امتیاز دهی وضعیت قرعه کشی به 'برگزاری قرعه کشی' تغییر خواهد کرد";
    }

    public function reportScoringError()
    {
//        try {
//            $developer = User::where('mobile', self::DEVELOPER)->first();
//            $developer->notify(new ScoringLotteryErrorNotification($this->lottery));
//            $this->lottery->lottery_status_id = LotteryStatus::WAIT_FOR_FIX_SCORING;
//            $this->lottery->save();
//        } catch (\Exception $exception) {
//            return 'در حال حاضر امکان ارسال پیام وجود ندارد';
//        }
        return 'امیاز دهی با خطا مواجه شده. جهت رفع مشکل با واحد فنی تماس بگیرید';
    }

    public function holdLottery()
    {
        try {
            Artisan::queue('alaaTv:holdLottery2', ['id' => $this->lottery->id]);
            $this->lottery->lottery_status_id = LotteryStatus::WAIT_FOR_HOLDING_LOTTERY;
            $this->lottery->save();
        } catch (Exception $exception) {
            return 'اجرای قرعه کشی با مشکل مواجه شد.';
        }
        return 'قرعه کشی در صف اجرا قرار گرفت. پس از اتمام وضعیت قرعه کشی به روز رسانی خواهد شد.';
    }

    public function waitForHoldingLottery()
    {
        return "قرعه کشی در حال انجام است. لطفا تا اتمام عملیات صبر بفرمایید. با پایان قرعه کشی وضعیت قرعه کشی به 'نمایش نتایج' تغییر خواهد کرد.";
    }

    public function reportHoldingError()
    {
//        try {
//            $developer = User::where('mobile', self::DEVELOPER)->first();
//            $developer->notify(new HoldingLotteryErrorNotification($this->lottery, auth()->id()));
//            $this->lottery->lottery_status_id = LotteryStatus::WAIT_FOR_FIX_HOLDING;
//            $this->lottery->save();
//        } catch (\Exception $exception) {
//            return 'در حال حاضر امکان ارسال پیام وجود ندارد';
//        }
//        return 'پیام شما برای توسعه دهنده سایت ارسال و پس از بررسی وضعیت قرعه کشی به روز رسانی خواهد شد.';
    }

}
