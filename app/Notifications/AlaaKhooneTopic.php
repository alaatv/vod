<?php

namespace App\Notifications;

use App\Broadcasting\MedianaPatternChannel;
use App\Classes\sms\MedianaMessage;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class AlaaKhooneTopic extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected const MEDIANA_PATTERN_CODE_MARKETING1 = 'pnxg2235clsqz53';

    public $timeout = 120;

    protected $user;

    private $title;
    private $titleLocation;
    private $link;

    /**
     * @param $title
     * @param $titleLocation
     * @param $link
     */
    public function __construct($title, $titleLocation, $link)
    {
        $this->title = $title;
        $this->titleLocation = $titleLocation;
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
            'title' => $this->title,
            'titleLocation' => $this->titleLocation,
            'link' => $this->link,
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
