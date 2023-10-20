<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class GiftGiven2 extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'u8i6r62ssc';

    public $timeout = 120;
    private $userFullName;
    private $lotteryName;
    private $prizeInfo;
    private $prize;

    /**
     * GiftGiven2 constructor.
     *
     * @param  string  $userFullName
     * @param  string  $lotteryName
     * @param  string  $prize
     * @param  string  $prizeInfo
     */
    public function __construct(string $userFullName, string $lotteryName, string $prize, string $prizeInfo)
    {
        $this->userFullName = $userFullName;
        $this->lotteryName = $lotteryName;
        $this->prizeInfo = $prizeInfo;
        $this->prize = $prize;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return [
            MedianaPatternChannel::class,
        ];
    }

    /**
     * @param $notifiable
     *
     * @return MedianaMessage
     */
    public function toMediana($notifiable)
    {
        return (new MedianaMessage())->content($this->msg())
            ->setInputData($this->getInputData())
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_INVOICE_PAID)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        return '';
    }

    private function getInputData(): array
    {
        return [
            'name' => $this->userFullName,
            'lotteryName' => $this->lotteryName,
            'prize' => $this->prize,
            'prizeInfo' => $this->prizeInfo
        ];
    }
}
