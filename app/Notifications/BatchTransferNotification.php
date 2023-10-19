<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use App\Traits\UserCommon;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class BatchTransferNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;
    use UserCommon;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'jq6xvox09z';

    public $timeout = 120;

    private $receiver;

    /**
     * BatchTransferNotification constructor.
     *
     * @param $receiver
     */
    public function __construct($receiver)
    {
        $this->receiver = $receiver;
    }


    public function via($notifiable)
    {
        return [
            MedianaPatternChannel::class,
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
            'name' => $this->getUserFullName($notifiable),
            'receiver' => $this->receiver,
            'supportLink' => action('Web\UserController@userOrders'),
        ];
    }
}
