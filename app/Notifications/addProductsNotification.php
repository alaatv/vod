<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class addProductsNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = '';


    private $products;
    private $user;
    private $specialTitle;
    private $link;

    public function __construct(string $products, string $specialTitle, string $link)
    {
        $this->products = $products;
        $this->specialTitle = $specialTitle;
        $this->link = $link;
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
            MedianaPatternChannel::class
        ];
    }

    public function toMediana($notifiable)
    {
        return (new MedianaMessage())
            ->setInputData($this->getInputData())
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_INVOICE_PAID)
            ->sendAt(Carbon::now());
    }

    private function getInputData(): array
    {
        return [
            'name' => $this->user->first_name ?: 'کاربر',
            'products' => $this->products,
            'special tile' => $this->specialTitle,
            'link' => $this->link,
        ];
    }
}
