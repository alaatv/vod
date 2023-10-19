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

class SendVerificationCodeToUnknownNumber extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const PATTERN_CODE = 'vqtenv23vj';

    /**
     * @var User
     */
    protected $user;
    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
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
            'name' => 'آلایی عزیز',
            'code' => $this->code,
        ];
    }
}
