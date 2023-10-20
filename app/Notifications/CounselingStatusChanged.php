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

class CounselingStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_COUNSELING_STATUS_CHANGED = '88vrw6uktl';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;

    private $orderStatus;

    public function __construct($orderStatus)
    {
        $this->orderStatus = $orderStatus;
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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_COUNSELING_STATUS_CHANGED)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        $messageCore =
            ' آلایی عزیز وضعیت سوال مشاوره ای شما به '.$this->orderStatus.' تغییر کرد.'."\n".'alaatv.com';

        return $messageCore;
    }

    private function getInputData(): array
    {
        return [
            'name' => 'آلایی',
            'request' => 'سوال مشاوره ای',
            'status' => $this->orderStatus,
            'supportLink' => '',
        ];
    }
}
