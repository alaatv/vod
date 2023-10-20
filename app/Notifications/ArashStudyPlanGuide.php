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

class ArashStudyPlanGuide extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_USER_MOBILE_VERIFIED = '1diaq3h275';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;
    /**
     * @var string
     */
    private $link1;
    /**
     * @var string
     */
    private $link2;
    /**
     * @var string
     */
    private $link3;
    /**
     * @var string
     */
    private $link4;

    /**
     * ArashStudyPlanGuide constructor.
     *
     * @param  string  $link1
     * @param  string  $link2
     * @param  string  $link3
     * @param  string  $link4
     */
    public function __construct(string $link1, string $link2, string $link3, string $link4)
    {
        $this->link1 = $link1;
        $this->link2 = $link2;
        $this->link3 = $link3;
        $this->link4 = $link4;
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
            'link_one' => $this->link1,
            'link_two' => $this->link2,
            'link_three' => $this->link3,
            'link_four' => $this->link4,
        ];
    }
}
