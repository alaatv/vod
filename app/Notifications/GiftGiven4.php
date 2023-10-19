<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class GiftGiven4 extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'ea12kjcr6q';

    public $timeout = 120;

    private $baseProduct;
    private $giftProduct;

    /**
     * CouponGift2 constructor.
     *
     * @param $baseProduct
     * @param $giftProduct
     */
    public function __construct($baseProduct, $giftProduct)
    {
        $this->baseProduct = $baseProduct;
        $this->giftProduct = $giftProduct;
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
            'baseProduct' => $this->baseProduct,
            'giftProduct' => $this->giftProduct,
        ];
    }
}
