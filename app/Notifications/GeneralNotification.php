<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class GeneralNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $timeout = 120;
    /**
     * @var Order
     */

    protected $user;
    private $pattern;
    /**
     * @var array
     *
     */
    private $params;
    private ?string $referenceType;
    private ?string $referenceId;

    /**
     * Create a new notification instance.
     *
     * @param  string  $pattern
     * @param  array  $params
     */
    public function __construct(string $pattern, array $params, ?string $referenceType, ?string $referenceId)
    {
        $this->pattern = $pattern;
        $this->params = $params;
        $this->referenceType = $referenceType;
        $this->referenceId = $referenceId;
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
            ->setInputData($this->params)
            ->setLogData($this->referenceType, $this->referenceId)
            ->setPatternCode($this->pattern)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        return '';
    }

}
