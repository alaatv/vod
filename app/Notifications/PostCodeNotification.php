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

class PostCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_POST_CODE = '6zgudygzau';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;

    private $code;

    /**
     * PostCodeNotification constructor.
     *
     * @param $code
     */
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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_POST_CODE)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        $messageCore = 'آلایی عزیز سلام،'."\n".'کد رهگیری پست شما'."\n".$this->code."\n".'است.';

        return $messageCore;
    }

    private function getInputData(): array
    {
        return [
            'name' => 'آلایی',
            'code' => $this->code,
            'supportLink' => '',
        ];
    }
}
