<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class GiveLotteryPoints extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'hfgif0xpbb';

    public $timeout = 120;

    protected $user;

    /**
     * @var int
     */
    protected $number1;

    /**
     * @var string
     */
    protected $product;

    /**
     * @var int
     */
    protected $number2;

    /**
     * @var string
     */
    protected $gift;

    /**
     * GiveLotteryPoints constructor.
     *
     * @param  int  $number1
     * @param  string  $product
     * @param  int  $number2
     * @param  string  $gift
     */
    public function __construct(int $number1, string $product, int $number2, string $gift)
    {
        $this->number1 = $number1;
        $this->product = $product;
        $this->number2 = $number2;
        $this->gift = $gift;
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
            'name' => $this->getUserFullName(),
            'number1' => $this->number1,
            'product' => $this->product,
            'number2' => $this->number2,
            'gift' => $this->gift,
        ];
    }

    /**
     * @return mixed
     */
    private function getUserFullName(): string
    {
        $userFullName = $this->user->full_name;
        return (isset($userFullName) && strlen($userFullName) > 0) ? $userFullName : 'آلایی';
    }

}
