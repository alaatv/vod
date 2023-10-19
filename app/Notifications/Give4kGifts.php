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

class Give4kGifts extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'wr4y0w81n0';

    public $timeout = 120;

    /**
     * @var Order
     */
    protected $name;
    protected $product1;
    protected $plan;
    protected $product2;
    protected $productLink;


    public function __construct(string $name, string $product1, string $plan, string $product2, string $productLink)
    {
        $this->name = $name;
        $this->product1 = $product1;
        $this->plan = $plan;
        $this->product2 = $product2;
        $this->productLink = $productLink;
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
        // todo: user set by calling class or should set automatically
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
            'name' => $this->name,
            'product1' => $this->product1,
            'product2' => $this->product2,
            'plan' => $this->plan,
            'productLink' => $this->productLink,
        ];
    }
}
