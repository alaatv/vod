<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-04-23
 * Time: 21:24
 */

namespace App\Classes\sms;

use App\Models\SmsProvider;
use App\Models\SmsProvider;
use App\Traits\Helper;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Exception;

class MedianaPatternClient implements SmsSenderClient
{
    use Helper;

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

    /**
     * @param  array  $params
     * @return array
     */
    public function send(array $params)
    {
        $to = Arr::get($params, 'toNum');
        if (is_null($to)) {
            throw new Exception('No receiver for SMS has been set');
        }

        $to = baseTelNo($to);
        $from = determineSMSOperator("0{$to}");
        $from = baseTelNo($from);

        // Important: The following section is very important and necessary. This is not mentioned in
        //  the Mediana ippanel package document. But it is essential to do it right.
        $data = array_map('strval', $params['inputData']);

        $returnData = [
            'result' => [
                'to' => [$to],
                'provider_id' => SmsProvider::filter([
                    'number' => $from, 'defaults' => true, 'enable' => true
                ])?->first()?->id,
                'pattern_code' => $params['patternCode'],
                'pattern_data' => $data,
                'log_data' => Arr::get($params, 'logData', []),
            ],
        ];

        $patternCode = $params['patternCode'];
        $patternData = $data;

        $info = $this->sendPatternSmsByApi($to, $from, $patternCode, $patternData);

        return array_merge($returnData, $info);
    }
}
