<?php

namespace App\Broadcasting;

use App\Classes\sms\MedianaMessage;
use App\Classes\sms\SmsSenderClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class MedianaPatternChannel
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
     * @throws GuzzleException
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routes['mobile'] ?? $this->getTo($notifiable);
        $message = $notification->toMediana($notifiable);

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
        $param = [
            //            'to' => json_encode([$to], JSON_UNESCAPED_UNICODE),
            'toNum' => $to,
            'patternCode' => trim(data_get($message, 'pattern_code')),
            'inputData' => data_get($message, 'input_data'),
            'logData' => data_get($message, 'log_data'),
            'op' => 'patternV2',
        ];
        if (isset($message->from)) {
            $param['from'] = $message->from;
        }

        return $param;
    }
}
