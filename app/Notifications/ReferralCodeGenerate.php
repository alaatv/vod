<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\ReferralRequest;
use App\Models\ReferralRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ReferralCodeGenerate extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_INVOICE_PAID = 'u8y36822h0o0824';

    public $timeout = 120;

    protected $referralRequest;
    protected $discount;
    protected $link;

    public function __construct(ReferralRequest $referralRequest, string $link)
    {
        $this->referralRequest = $referralRequest;
        $this->link = $link;
        $this->discount = $this->referralRequest->discount;
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
            'totalDiscount' => number_format($this->discount * $this->referralRequest->numberOfCodes).' تومان',
            'discountUnit' => number_format($this->discount).' تومان',
            'link' => $this->link,
        ];
    }

    /**
     * @return mixed
     */
    private function getUserFullName(): string
    {
        $userFullName = optional($this->getReferralRequestUser())->full_name;
        return (isset($userFullName) && strlen($userFullName) > 0) ? $userFullName : 'آلایی';
    }

    private function getReferralRequestUser(): User
    {
        return $this->referralRequest->owner;
    }
}
