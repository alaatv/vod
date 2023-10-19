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

class ProductAddedToUser extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_USER_MOBILE_VERIFIED = 'jr1wz2x8zv';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;
    private $productName;
    private $infoStatement;
    private $link;

    /**
     * HasntBoughtProduct constructor.
     *
     * @param  string  $productName
     * @param  string  $infoStatement
     * @param  string  $link
     */
    public function __construct(string $productName, string $infoStatement = '', string $link = null)
    {
        $this->productName = $productName;
        $this->infoStatement = $infoStatement;
        $this->link = $link;
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
            'products' => $this->productName,
            'specialTitle' => $this->infoStatement,
            'link' => $this->link ?? 'https://alaatv.com/asset',
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
