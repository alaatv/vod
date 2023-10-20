<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class _3aExamNotification2 extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;


    public const MEDIANA_PATTERN_CODE_USER_SEND_VERIFICATION_CODE = 'ydcwaf52mv';
    public const VALID_PRODUCTS = [
        Product::_3A_JAMBANDI_DAVAZDAHOM_ENSANI_TERM1_1401 => [
            'bookletNames' => 'آزمون ترم اول انسانی سه آ',
            'omoomiBookletLinks' => 'http://alaa.tv/qRqZiw',
            'ekhtesasiBookletLinks' => 'http://alaa.tv/dq7wrt',
        ],
        Product::_3A_JAMBANDI_DAVAZDAHOM_RIYAZI_TERM1_1401 => [
            'bookletNames' => 'نام دانش آموز',
            'omoomiBookletLinks' => 'http://alaa.tv/275Gwus',
            'ekhtesasiBookletLinks' => 'http://alaa.tv/RbUx9p',
        ],
        Product::_3A_JAMBANDI_DAVAZDAHOM_TAJROBI_TERM1_1401 => [
            'bookletNames' => 'آزمون ترم اول تجربی سه آ',
            'omoomiBookletLinks' => 'http://alaa.tv/rMgoTj',
            'ekhtesasiBookletLinks' => 'http://alaa.tv/2myALb6',
        ],
    ];
    public $timeout = 120;
    /**
     * @var User
     */
    protected $user;

    /**
     * _3aExamNotification constructor.
     */
    public function __construct(private int $productId)
    {
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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_USER_SEND_VERIFICATION_CODE)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        return '';
    }

    private function getInputData(): array
    {
        return [
            'name' => $this->getFullName(),
            'bookletName' => self::VALID_PRODUCTS[$this->productId]['bookletNames'],
            'omoomiBookletLink' => self::VALID_PRODUCTS[$this->productId]['omoomiBookletLinks'],
            'ekhtesasiBookletLink' => self::VALID_PRODUCTS[$this->productId]['ekhtesasiBookletLinks'],
        ];
    }

    private function getFullName()
    {
        return $this->user->full_name ?? 'آلایی';
    }
}
