<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Exception;
use SoapClient;

class SendPatternSMS implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const SMS_PATTERN_CODE = 'xqp8wcgc10';
    /**
     *
     * @var string
     */
    private $mobileNumber;
    /**
     *
     * @var string
     */
    private $patternCode;
    /**
     *
     * @var array
     */
    private $inputData;

    /**
     * SendPatternSMS constructor.
     *
     * @param  string  $mobileNumber
     * @param  string  $patternCode
     * @param  array  $inputData
     */
    public function __construct(string $mobileNumber, string $patternCode, array $inputData)
    {
        $this->mobileNumber = $mobileNumber;
        $this->patternCode = $patternCode;
        $this->inputData = $inputData;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $client = new SoapClient('http://188.0.240.110/class/sms/wsdlservice/server.php?wsdl');
        $username = config('services.medianaSMS.normal.userName');
        $pass = config('services.medianaSMS.normal.password');
        $fromNum = determineSMSOperator($this->mobileNumber);
        $toNum = [$this->mobileNumber];

        $response = $client->sendPatternSms($fromNum, $toNum, $username, $pass, $this->patternCode, $this->inputData);
        if (!is_string($response)) {
            Log::channel('sendPatternSMS')->info('Error on sending sms to user: '.$this->mobileNumber);
            Log::channel('sendPatternSMS')->info('Response : '.implode(',', $response));
            throw new Exception('Error response from SMS provider for mobile '.$this->mobileNumber);
        }

        Log::channel('sendPatternSMS')->info("SMS Sent to $this->mobileNumber , bulk:  $response");
        return null;
    }
}
