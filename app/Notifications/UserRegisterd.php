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

class UserRegisterd extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_USER_REGISTERD = 'ss2qf9hdi3';

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
        if (!isset($this->user)) {
            $this->user = $notifiable;
        }

        return (new MedianaMessage())->content($this->msg())
            ->setInputData($this->getInputData())
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_USER_REGISTERD)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        $gender = $this->getGender();
        $messageCore =
            'به آلاء خوش آمدید، اطلاعات کاربری شما:'."\n".'نام کاربری:'."\n".$this->user->mobile."\n".'رمز عبور:'."\n".$this->user->nationalCode."\n".'پشتیبانی:'."\n".'https://AlaaTV.com/t';
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

    private function getInputData(): array
    {

        return [
            'username' => $this->user->mobile,
            'password' => $this->user->nationalCode,
            'supportLink' => 'https://alaatv.com/t',
        ];
    }
}
