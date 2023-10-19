<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class ProductChoiceArash1401 extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_USER_MOBILE_VERIFIED = '4ny6qxg527piiq9';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;

    private $product;

    /**
     * @param $product
     */
    public function __construct($product)
    {
        $this->product = $product;
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
//            'mail',
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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_USER_MOBILE_VERIFIED)
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
            'product' => $this->product,
            'product_choice_one' => 'همایش آرش فیزیک آقای طلوعی',
            'num_one' => '1007',
            'product_choice_two' => 'همایش آرش فیزیک آقای کازرانیان',
            'num_two' => '1008',
            'product_choice_three' => 'همایش آرش فیزیک آقای یاری',
            'num_three' => '1009',
        ];
    }

    /**
     * @return mixed
     */
    private function getUserFullName(): string
    {
        $userFullName = optional($this->user)->full_name;
        return (isset($userFullName)) ? $userFullName : 'آلایی';
    }
}
