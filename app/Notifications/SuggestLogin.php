<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class SuggestLogin extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const PATTERN_CODE = 'f8yje2iov9';

    /**
     * @var User
     */
    protected $user;

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
            ->setPatternCode(self::PATTERN_CODE)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        return 'آلایی عزیز، شماره موبایل شما در آلاء تایید شد.';
    }

    private function getInputData(): array
    {
        return [
            'link' => 'alaatv.com/login',
        ];
    }
}
