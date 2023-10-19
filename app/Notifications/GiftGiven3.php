<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class GiftGiven3 extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'bwwxd08dde';

    public $timeout = 120;

    private $giftName;
    private $statement;
    private $userFullName;

    /**
     * GiftGiven2 constructor.
     *
     * @param  string  $giftName
     * @param  string  $statement
     * @param  string  $userFullName
     */
    public function __construct(string $giftName, string $statement, string $userFullName)
    {
        $this->giftName = $giftName;
        $this->statement = $statement;
        $this->userFullName = $userFullName;
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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_INVOICE_PAID)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        return '';
    }

    private function getInputData(): array
    {
        return [
            'name' => $this->userFullName,
            'product' => $this->giftName,
            'productCondition' => $this->statement,
        ];
    }
}
