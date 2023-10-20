<?php

namespace App\Notifications;

use App\Broadcasting\MedianaChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class GiftGiven extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $timeout = 120;

    /**
     * @var int
     */
    protected $giftCost;

    /**
     * @var string
     */
    protected $partialMessage;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param  int  $giftCost
     */
    public function __construct($giftCost, $partialMessage = null)
    {
        $this->partialMessage = $partialMessage;
        $this->giftCost = $giftCost;
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
        $partialMessage = 'به عنوان هدیه به کیف پول شما افزوده شد.';
        if (isset($this->partialMessage)) {
            $partialMessage = $this->partialMessage;
        }
        $gender = $this->getGender();
        $messageCore =
            'مبلغ '.$this->giftCost.' تومان '.$partialMessage."\n".'آلاء'."\n".'پشتیبانی:'."\n".'https://AlaaTV.com/t';
        $message = 'سلام '.$gender.$this->user->full_name."\n".$messageCore;

        return $message;
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
