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

class ProductChoiceAbrisham extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_USER_MOBILE_VERIFIED = '55o7ti3zkl';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;

    private $purchasedProduct;
    private $choice1Name;
    private $num1;
    private $choice2Name;
    private $num2;

    /**
     * @param $purchasedProduct
     * @param $choice1Name
     * @param $num1
     * @param $choice2Name
     * @param $num2
     */
    public function __construct($purchasedProduct, $choice1Name, $num1, $choice2Name, $num2)
    {
        $this->purchasedProduct = $purchasedProduct;
        $this->choice1Name = $choice1Name;
        $this->num1 = $num1;
        $this->choice2Name = $choice2Name;
        $this->num2 = $num2;
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
            'product' => $this->purchasedProduct,
            'product_choice_one' => $this->choice1Name,
            'num_one' => $this->num1,
            'product_choice_two' => $this->choice2Name,
            'num_two' => $this->num2,
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
