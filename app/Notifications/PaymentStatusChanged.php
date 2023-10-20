<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class PaymentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_ORDER_STATUS_CHANGED = '';

    private $paymentStatus;
    private $user;

    public function __construct($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $this->user = $notifiable;
        return [
            MedianaPatternChannel::class,

        ];
    }

    public function toMediana($notifiable)
    {
        return (new MedianaMessage())
            ->setInputData($this->getInputData())
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_ORDER_STATUS_CHANGED)
            ->sendAt(Carbon::now());
    }

    private function getInputData(): array
    {
        return [
            'name' => $this->user->full_name,
            'status' => $this->paymentStatus,
        ];
    }

}
