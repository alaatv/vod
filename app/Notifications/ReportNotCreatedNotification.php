<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReportNotCreatedNotification extends Notification
{
    use Queueable;

    public const MEDIANA_PATTERN_CODE_INVOICE_PAID = '';

    public function __construct()
    {
    }

    public function via($notifiable)
    {
        return [
            MedianaPatternChannel::class
        ];
    }

    public function toMediana($notifiable)
    {
        return (new MedianaMessage())
            ->setInputData($this->getInputData($notifiable))
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_INVOICE_PAID)
            ->sendAt(Carbon::now());
    }

    private function getInputData($notifiable): array
    {
        return [
            'name' => $notifiable->firstName ?: 'کاربر',
        ];
    }
}
