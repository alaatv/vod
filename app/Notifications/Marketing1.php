<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class Marketing1 extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_MARKETING1 = 'b9qszukdvq';

    public $timeout = 120;

    protected $user;
    private $code;
    private $discount;
    private $expirateDate;
    private $supportLink;

    /**
     * Marketing1 constructor.
     *
     * @param $code
     * @param $discount
     * @param $expirateDate
     * @param $supportLink
     */
    public function __construct(string $code, string $discount, string $expirateDate, string $supportLink)
    {
        $this->code = $code;
        $this->discount = $discount;
        $this->expirateDate = $expirateDate;
        $this->supportLink = $supportLink;
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

        if (!isset($this->user)) {
            $this->user = $notifiable;
        }

        return (new MedianaMessage())->content($this->msg())
            ->setInputData($this->getInputData())
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_MARKETING1)
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
            'discount' => $this->discount,
            'expirateDate' => $this->expirateDate,
            'code' => $this->code,
            'supportLink' => $this->supportLink,
        ];
    }

    /**
     * @return mixed
     */
    private function getUserFullName(): string
    {
        $userFullName = optional($this->user)->full_name;
        return (isset($userFullName) && strlen($userFullName) > 0) ? $userFullName : 'آلایی';
    }
}
