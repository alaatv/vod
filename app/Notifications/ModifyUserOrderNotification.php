<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ModifyUserOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'eiSoh8lo1U';
    public const FARSI_MESSAGE_LENGTH_LIMIT = 70;
    public $timeout = 120;
    protected $createdGifts;
    protected $userName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($createdGifts, string $userName)
    {
        $this->createdGifts = $createdGifts;
        $this->userName = $userName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [
            MedianaPatternChannel::class
        ];
    }

    /**
     * @param $notifiable
     *
     * @return MedianaMessage
     */
    public function toMediana($notifiable)
    {
        return (new MedianaMessage())
            ->setInputData($this->getInputData())
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_INVOICE_PAID)
            ->sendAt(Carbon::now());
    }


    private function getInputData()
    {
        return [
            'name' => $this->userName,
            'message' => $this->createdGifts,
        ];
    }

    private function msg(): string
    {
        if ($this->createdGifts === true) {
            return ' آلایی عزیز محصولات سفارش شما بروزرسانی شد';
        }
        $message = implode("\n", $this->createdGifts);
        if (strlen($message) > self::FARSI_MESSAGE_LENGTH_LIMIT - strlen($prefix = "آلایی عزیز محصولات زیر به عنوان هدیه به حساب شما افزوده شد\n")) {
            return 'آلایی عزیز'.count($this->createdGifts).'هدیه به سبد شما افزوده شد';
        }
        return $prefix.$message;
    }
}
