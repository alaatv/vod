<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class FreeInternetAccept extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_FREE_INTERNET_ACCEPT = 812;

    public $timeout = 120;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        //$this->user = $notifiable;
        return [
            MedianaPatternChannel::class,
            'mail',
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())->line($this->msg())
            ->action('دریافت فایل راهنما', url('https://alaatv.com/v/asiatech'));
    }

    private function msg(): string
    {
        $messageCore =
            'آلایی عزیز با درخواست اینترنت رایگان شما موافقت شد'."\n".'دریافت فایل راهنما از'.'https://alaatv.com/v/asiatech';

        return $messageCore;
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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_FREE_INTERNET_ACCEPT)
            ->sendAt(Carbon::now());
    }

    private function getInputData(): array
    {
        return [
            'https://alaatv.com/v/asiatech' => 'https://alaatv.com/v/asiatech',
        ];
    }
}
