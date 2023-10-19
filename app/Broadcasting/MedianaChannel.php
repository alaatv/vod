<?php

namespace App\Broadcasting;

use App\Classes\sms\MedianaMessage;
use App\Classes\sms\SmsSenderClient;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class MedianaChannel
{
    use SerializesModels;

    /**
     * The client instance.
     *
     * @var SmsSenderClient
     */
    protected $client;

    /**
     * Create a new channel instance.
     *
     * @param  SmsSenderClient  $client
     */
    public function __construct(SmsSenderClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return array
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $this->getTo($notifiable);
        $message = $notification->toMediana($notifiable);
        if (is_string($message)) {
            $message = new MedianaMessage($message);
        }

        return $this->client->send($this->buildParams($message, $to));
    }

    /**
     * Get phone number.
     *
     * @param $notifiable
     *
     * @return mixed
     */
    protected function getTo($notifiable)
    {
        if ($to = $notifiable->routeNotificationForPhoneNumber()) {
            return $to;
        }

        return $notifiable->phone_number;
    }

    /**
     * Build up params.
     *
     * @param  MedianaMessage  $message
     * @param  string  $to
     *
     * @return array
     */
    protected function buildParams(MedianaMessage $message, $to)
    {
        $optionalFields = array_filter([
            //            'time'    => data_get($message, 'sendAt'),
            //            'input_data' => data_get($message , 'input_data'),

        ]);
        $param = array_merge([
            'to' => json_encode([$to], JSON_UNESCAPED_UNICODE),
            'message' => trim(data_get($message, 'content')),
            'op' => 'send',
            //            'pattern_code' => trim(data_get($message , 'pattern_code')),

        ], $optionalFields);

        if (isset($message->from)) {
            $param['from'] = $message->from;
        }

        return $param;
    }
}
