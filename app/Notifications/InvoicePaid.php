<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class InvoicePaid extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'et0jpn43fn';

    public $timeout = 120;

    /**
     * @var Order
     */
    protected $invoice;
    protected $assetLink;

    /**
     * Create a new notification instance.
     *
     * @param  Order  $invoice
     * @param  string|null  $assetLink
     */
    public function __construct(Order $invoice, string $assetLink = null)
    {
        $this->invoice = $invoice;
        $this->assetLink = $assetLink;
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
            'code' => $this->invoice->id,
            'name' => $this->getUserFullName(),
            'assetLink' => $this->assetLink ?? route('web.user.asset'),
            'supportLink' => 'https://AlaaTV.com/t',
        ];
    }

    /**
     * @return mixed
     */
    private function getUserFullName(): string
    {
        $userFullName = optional($this->getInvoiceUser())->full_name;
        return (isset($userFullName) && strlen($userFullName) > 0) ? $userFullName : 'آلایی';
    }

    private function getInvoiceUser(): User
    {
        return $this->invoice->user;
    }
}
