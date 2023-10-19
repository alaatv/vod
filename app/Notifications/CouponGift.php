<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\Coupon;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class CouponGift extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'anj4tgh80m';

    public $timeout = 120;

    /**
     * @var Order
     */
    protected $coupon;

    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param  Coupon  $coupon
     */
    public function __construct(Coupon $coupon)
    {
        $this->coupon = $coupon;
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
            'name' => 'آلایی',
            'offer' => 'تخفیف 70% همایش ریاضی تجربی آرش',
            'product' => 'راه ابریشم',
            'code' => $this->coupon->code,
        ];
    }
}
