<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-04-23
 * Time: 21:24
 */

namespace App\Classes\sms;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Exception;

class MedianaClient implements SmsSenderClient
{
    /**
     * The SMS Number to send the message from 021 operator.
     *
     * @var int
     */
    protected $number021;

    /**
     * The SMS Number to send the message from 1000 operator.
     *
     * @var int
     */
    protected $number1000;

    /**
     * Username for SMS Gateway.
     *
     * @var string
     */
    protected $userName;

    /**
     * Password for SMS Gateway.
     *
     * @var string
     */
    protected $password;

    protected $url;

    /**
     * The HTTP Client instance.
     *
     * @var HttpClient
     */
    protected $http;

    public function __construct(HttpClient $http, $userName, $password, $number021, $number1000, $url)
    {
        $this->number021 = $number021;
        $this->number1000 = $number1000;
        $this->userName = $userName;
        $this->http = $http;
        $this->password = $password;
        $this->url = $url;
    }

    /**
     * @param  array  $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function send(array $params)
    {
        $to = Arr::get($params, 'to');
        if (is_null($to)) {
            throw new Exception('No receiver for SMS has been set');
        }

        $from = determineSMSOperator('0'.$to);

        $url = $this->url;

        $base = [
            'uname' => $this->userName,
            'pass' => $this->password,
            'from' => $from,
        ];
        if (isset($params['from'])) {
            unset($base['from']);
        }

        $params = array_merge($base, $params);

        try {
            $response = $this->http->request('POST', $url, [
                'form_params' => $params,
            ]);
        } catch (GuzzleException $e) {
            throw $e;
        }

        return json_decode((string) $response->getBody(), true);
    }
}
