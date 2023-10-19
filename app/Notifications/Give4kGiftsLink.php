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

class Give4kGiftsLink extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'g4eelall2a';

    public $timeout = 120;

    /**
     * @var Order
     */
    protected $name;
    protected $product1;
    protected $plan;
    protected $offered_user;
    protected $productLink;


    public function __construct(string $name, string $product1, string $plan, string $offered_user, string $productLink)
    {
        $this->name = $name;
        $this->product1 = $product1;
        $this->plan = $plan;
        $this->offered_user = $offered_user;
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
            'offered_user' => $this->offered_user,
            'plan' => $this->plan,
            'productLink' => $this->productLink,
        ];
    }
}
