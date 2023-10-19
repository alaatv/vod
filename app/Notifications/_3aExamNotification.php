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

class _3aExamNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;


    public const MEDIANA_PATTERN_CODE_USER_SEND_VERIFICATION_CODE = '3kuam0tqxm';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;

    /**
     * _3aExamNotification constructor.
     */
    public function __construct()
    {
        $this->queue = 'default2';
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
            'user' => $this->getUserFullName(),
            'time' => 'المپیاد تخصصی پایه یازدهم که تا ساعت 12:00 فرصت دارید شروع کنید(از لحظه شروع 180 دقیقه وقت دارید)',
            'link' => 'soalaa.com',
            'mobile' => $this->getUsername(),
            'password' => $this->getUserNationalCode(),
            'platform' => 'ایتا',
            'support' => 'http://alaa.tv/2ZM35',
        ];
    }

    /**
     * @return mixed
     */
    private function getUserFullName(): string
    {
        $userFullName = optional($this->user)->firstName;
        $userFullName = !isset($userFullName) ? optional($this->user)->lastName : $userFullName;
        return (isset($userFullName)) ? $userFullName : 'آلایی';
    }

    private function getUsername(): string
    {
        $mobile = optional($this->user)->mobile;
        return (isset($mobile)) ? $mobile : '';
    }

    private function getUserNationalCode(): string
    {
        $nationalCode = optional($this->user)->nationalCode;
        return (isset($nationalCode)) ? $nationalCode : '';
    }
}
