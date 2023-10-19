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

class ServiceGiven extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_USER_MOBILE_VERIFIED = 'r3w4u3tobn';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * ServiceGiven constructor.
     *
     * @param  string  $serviceName
     */
    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
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
//            'mail',
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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_USER_MOBILE_VERIFIED)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        return 'آلایی عزیز، شماره موبایل شما در آلاء تایید شد.';
    }

    private function getInputData(): array
    {
        return [
            'name' => 'راه ابریشمی',
            'serviceName' => $this->serviceName,
            'supportLink' => 'alaatv.com',
        ];
    }
}
