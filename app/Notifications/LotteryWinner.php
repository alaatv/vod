<?php

namespace App\Notifications;

use App\Broadcasting\MedianaChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\Lottery;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class LotteryWinner extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $timeout = 120;

    /**
     * @var int
     */
    protected $rank;

    /**
     * @var string
     */
    protected $prize;

    /**
     * @var string
     */
    protected $memorial;

    /**
     * @var Lottery
     */
    protected $lottery;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param  Lottery  $lottery
     * @param                $rank
     * @param                $prize
     * @param                $memorial
     */
    public function __construct(Lottery $lottery, $rank, $prize, $memorial)
    {
        $this->lottery = $lottery;
        $this->rank = $rank;
        $this->prize = $prize;
        $this->memorial = $memorial;
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
        $this->user = $notifiable;

        return [
            MedianaChannel::class,
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
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        $lotteryName = $this->lottery->displayName;
        $rank = $this->rank;
        $prize = $this->prize;
        $memorial = $this->memorial;
        $gender = $this->getGender();

        if (strlen($prize) > 0) {
            $messageCore =
                'شما برنده '.$rank.' در قرعه کشی '.$lotteryName.' شده اید. جایزه شما '.$prize.' می باشد و در سریع ترین زمان به شما تقدیم خواهد شد.';
        } else {
            if (strlen($memorial) > 0) {
                $messageCore =
                    'شما در قرعه کشی '.$lotteryName.' شرکت داده شدید و متاسفانه چیزی برنده نشدید. به رسم یادبود '.$memorial.' تقدیمتان شده است.';
            } else {
                $messageCore = 'شما در قرعه کشی '.$lotteryName.' شرکت داده شدید و متاسفانه برنده نشدید.';
            }
        }

        $messageCore = $messageCore."\n".'آلاء'."\n".'alaatv.com';

        return 'سلام '.$gender.$this->user->full_name."\n".$messageCore;
    }

    /**
     * @return string
     */
    private function getGender(): string
    {
        if (!isset($this->user->gender_id)) {
            return '';
        }
        if ($this->user->gender->name == 'خانم') {
            return 'خانم ';
        }
        if ($this->user->gender->name == 'آقا') {
            return 'آقای ';
        }

        return '';
    }
}
