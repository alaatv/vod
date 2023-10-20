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

class VerifyMobile extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_USER_SEND_VERIFICATION_CODE = 'h18gfce5c7fm524';

    public $timeout = 120;

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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_USER_SEND_VERIFICATION_CODE)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        $messageCore =
            'کد تایید شماره موبایل شما در آلاء:'."\n".$this->user->getMobileVerificationCode()."\n".'alaatv.com';
        $message = $messageCore;

        return $message;
    }

    private function getInputData(): array
    {
        return [
            'name' => $this->getUserFullName(),
            'code' => $this->user->getMobileVerificationCode(),
            'siteDomain' => 'alaatv.com',
        ];
    }

    /**
     * @return mixed
     */
    private function getUserFullName(): string
    {
        $userFullName = optional($this->user)->full_name;
        return (isset($userFullName)) ? $userFullName : 'آلایی';
    }
}
