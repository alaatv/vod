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

class PreventRegisterParchamTicketByApp extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const MEDIANA_PATTERN_CODE_USER_MOBILE_VERIFIED = 'ona9ra4l53';

    public int $timeout = 120;
    protected User $user;
    private string $ticketLink;
    private string $ticketDepartment;

    /**
     * Parcham constructor.
     *
     * @param  string  $ticketLink
     * @param  string  $ticketDepartment
     */
    public function __construct(string $ticketLink, string $ticketDepartment)
    {
        $this->ticketLink = $ticketLink;
        $this->ticketDepartment = $ticketDepartment;
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
            'ticketDepartment' => $this->ticketDepartment,
            'ticketLink' => $this->ticketLink,
        ];
    }
}
