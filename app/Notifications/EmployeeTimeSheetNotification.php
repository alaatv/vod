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

class EmployeeTimeSheetNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_EMPLOYEE_TIME_SHEET = 'x7r2ty1ah4';

    public $timeout = 120;

    /**
     * @var User
     */
    protected $user;

    private $date;

    private $in;

    private $out;

    private $mh;

    private $eh;

    /**
     * EmployeeTimeSheetNotification constructor.
     *
     * @param $date
     * @param $in
     * @param $out
     * @param $mh
     * @param $eh
     */
    public function __construct($date, $in, $out, $mh, $eh)
    {
        $this->date = $date;
        $this->in = $in;
        $this->out = $out;
        $this->mh = $mh;
        $this->eh = $eh;
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
            ->setPatternCode(self::MEDIANA_PATTERN_CODE_EMPLOYEE_TIME_SHEET)
            ->sendAt(Carbon::now());
    }

    private function msg(): string
    {
        $messageCore =
            'سلام '.$this->user->firstName.' جان'."\n".$this->date."\n".'از'.' '.$this->in.' '.'تا'.$this->out."\n".'موظفی'.' '.$this->mh."\n".'اضافه'.' '.$this->eh;

        return $messageCore;
    }

    private function getInputData(): array
    {
        return [
            'name' => $this->user->firstName,
            'inTime' => $this->in,
            'outTime' => $this->out,
            'shiftTime' => $this->mh,
            'overTime' => $this->eh,
            'date' => $this->date,
        ];
    }
}
