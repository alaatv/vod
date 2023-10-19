<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;


class TelescopeFCM extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public const NOTIFICATION_IMAGE = 'https://nodes.alaatv.com/upload/telescope.png';
    public const NOTIFICATION_MODE = 'subscription';
    public const NOTIFICATION_TYPE = 'zamankoob';
    private $user;
    private $subscriptionDuration;

    /**
     * PurchasedTimepointSucscriptionFCM constructor.
     *
     * @param $subscriptionDuration
     */
    public function __construct(?string $subscriptionDuration)
    {
        $this->subscriptionDuration = $subscriptionDuration ?? '';
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $this->user = $notifiable;
        return [FcmChannel::class];
    }

    public function toFcm($notifable)
    {
        return FcmMessage::create()
            ->setData([
                'mode' => self::NOTIFICATION_MODE,
//                'body' => $this->getBody(),
//                'title' => $this->getTitle(),
                'image_url' => self::NOTIFICATION_IMAGE,
                'link_url' => '',
                'reminder_at' => '',
                'timer' => '0',
                'discount' => '',
                'subscription_type' => self::NOTIFICATION_TYPE,
            ])
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($this->getTitle())
                ->setBody($this->getBody())
                ->setImage(self::NOTIFICATION_IMAGE)
            )
//            ->setAndroid(
//                AndroidConfig::create()
//                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
//                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
//            )->setApns(
//                ApnsConfig::create()
//                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios')))
            ;

    }

    private function getTitle(): string
    {
        return 'زمان کوب آلاء';
    }

    private function getBody(): string
    {
        return $this->getUserName().' عزیز اشتراک زمان کوب شما برای مدت '.$this->subscriptionDuration.' فعال شد';
    }

    /**
     * @return mixed
     */
    private function getUserName(): string
    {
        $userFullName = optional($this->user)->firstName;
        return (isset($userFullName)) ? $userFullName : 'آلایی';
    }
}
