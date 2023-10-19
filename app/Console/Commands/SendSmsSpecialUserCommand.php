<?php

namespace App\Console\Commands;

use App\Http\Controllers\Web\HomeController;
use App\Models\User;
use App\Traits\APIRequestCommon;
use App\Traits\CharacterCommon;
use App\Traits\DateTrait;
use App\Traits\Helper;
use App\Traits\ProductCommon;
use App\Traits\RequestCommon;
use App\Traits\UserCommon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class SendSmsSpecialUserCommand extends Command
{
    use Helper;
    use APIRequestCommon;
    use ProductCommon;
    use CharacterCommon;
    use UserCommon;
    use RequestCommon;
    use DateTrait;

    public const CHUNK_THRESHOLD = 10000;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:send:bulkSMS';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sendSms';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::where('id', '>', 100000)->get();

        $usersCount = $users->count();

        if (!$this->confirm("$usersCount users fount, Do you wish to continue?", true)) {
            return 0;
        }

        $progress = $this->output->createProgressBar($usersCount);

        $this->line("\n");

        $progress->start();

        $users->chunk(self::CHUNK_THRESHOLD)->each(function ($users) use ($progress) {

            $smsNumber = config('services.medianaSMS.normal.from');
            $message = 'سلام آلایی
ساعت 8:30فردا 26فروردین ماه، دومین مرحله آزمون سه‌آ، ارزیابی مباحث پایه برگزار میشه
ثبت نام رایگان:
http://alaa.tv/6To6M';
            $usersId = collect($users)->pluck('id');
            $relatives = [0];

            $from021 = config('services.medianaSMS.normal.from');


            $users = User::whereIn('id', $usersId)->get();
            if ($users->isEmpty()) {
                return response()->json([], Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS);
            }


            $mobiles = [];
            $otherMobiles = [];
            /** @var User $user */
            foreach ($users as $user) {
                $mobile = [];
                if (in_array(0, $relatives)) {
                    $operator = determineUserMobileOperator($user->mobile);
                    $mobileLTrim = ltrim($user->mobile, '0');
                    $mobile['mobile'] = $mobileLTrim;
                    $mobile['id'] = $user->id;
                    if ($smsNumber != $from021) {
                        array_push($mobiles, $mobile);
                    } else {
                        if (strcmp($operator, '021') == 0) {
                            array_push($mobiles, $mobile);
                        } elseif (strcmp($operator, '1000') == 0) {
                            array_push($otherMobiles, $mobile);
                        }
                    }
                }
                if (in_array(1, $relatives) && !$user->contacts->isEmpty()) {
                    $fatherMobiles = $user->contacts
                        ->where('relative_id', 1)
                        ->first()
                        ->phones
                        ->where('phonetype_id', 1)
                        ->sortBy('priority');
                    if (!$fatherMobiles->isEmpty()) {
                        foreach ($fatherMobiles as $fatherMobile) {
                            $operator = determineUserMobileOperator($fatherMobile->phoneNumber);
                            $fatherMobileLTrim = ltrim($fatherMobile->phoneNumber, '0');
                            $mobile['mobile'] = $fatherMobileLTrim;
                            $mobile['id'] = $user->id;
                            if ($smsNumber != $from021) {
                                array_push($mobiles, $mobile);
                            } else {
                                if (strcmp($operator, '021') == 0) {
                                    array_push($mobiles, $mobile);
                                } elseif (strcmp($operator, '1000') == 0) {
                                    array_push($otherMobiles, $mobile);
                                }
                            }
                        }
                    }
                }
                if (!(in_array(2, $relatives) && !$user->contacts->isEmpty())) {
                    continue;
                }
                $motherMobiles = $user->contacts->where('relative_id', 2)
                    ->first()->phones->where('phonetype_id', 1)
                    ->sortBy('priority');
                if (!$motherMobiles->isEmpty()) {
                    foreach ($motherMobiles as $motherMobile) {
                        $operator = determineUserMobileOperator($motherMobile->phoneNumber);
                        $motherMobileLTrim = ltrim($motherMobile->phoneNumber, '0');
                        $mobile['mobile'] = $motherMobileLTrim;
                        $mobile['id'] = $user->id;
                        if ($smsNumber != $from021) {
                            array_push($mobiles, $mobile);
                        } else {
                            if (strcmp($operator, '021') == 0) {
                                array_push($mobiles, $mobile);
                            } elseif (strcmp($operator, '1000') == 0) {
                                array_push($otherMobiles, $mobile);
                            }
                        }
                    }
                }

            }

            if (empty($mobiles)) {

                $progress->advance();
                return;
            }
            $smsInfo = [
                'message' => $message,
                'to' => $mobiles,
                'from' => $smsNumber,
            ];


            dump($this->send($smsInfo));


            $progress->advance();
        });

        $progress->finish();

        $this->line("\n");

    }


    public function send($params)
    {
        if (!isset($params['to']) || !count($params['to'])) {
            return [
                'error' => true,
                'message' => 'No receiver determined',
                'result' => null,
            ];
        }

        $fromBaseTesNo = baseTelNo($params['from']);
        $transmitter = config('services.medianaSMS.normal.from');
        if (isset($params['from'])) {
            $transmitter = "98{$fromBaseTesNo}";
        }

        $recipients = [];
        foreach ($params['to'] as $recipient) {
            $recipients[] = '98'.baseTelNo($recipient['mobile']);
        }

        $returnData = [
            'result' => [
                'to' => $recipients,
            ],
            'message' => $params['message'],
        ];


        try {
//            $client = $this->ippanelClientObj();
//            $response = $client->send($transmitter, $recipients, $params['message']);
            $response = $this->medianaSendSMS([
                'to' => $recipients,
                'message' => $params['message']
            ]);
            if (!$response['error']) {
                $returnData['error'] = false;
                $returnData['has_response'] = true;
                $returnData['response'] = $response;
                return $returnData;
            }
            $returnData['error'] = true;
            $returnData['has_response'] = true;
            $returnData['response'] = $response;
            Log::channel('medianaIppanelApi')->error('Api response error - Sending bulk sms. Error message: '.json_encode($response['message']));

        } catch (Exception $e) {
            $returnData['error'] = true;
            $returnData['has_response'] = false;
            $returnData['response'] = $e;
            Log::channel('medianaIppanelApi')->error('Api fail - Sending bulk sms. Exception message: '.json_encode($e));
        }

        return $returnData;
    }

    public function medianaSendSMS(array $params)
    {
        $url = '188.0.240.110/services.jspd';

        if (!isset($params['to'])) {
            return [
                'error' => true,
                'message' => 'No receiver determined',
            ];
        }

        $rcpt_nm = $params['to'];

        $param = [
            'uname' => 'sanatisharif',
            'pass' => 'mediana@lenovo',
            'from' => '982162013',
            'message' => $params['message'],
            'to' => json_encode($rcpt_nm),
            'op' => 'send',
        ];

        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($handler, CURLOPT_POSTFIELDS, $param);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($handler);
        $response = json_decode($response);
        $res_code = $response[0];
        $res_data = $response[1];

        switch ($res_code) {
            case 0 :
                return [
                    'error' => false,
                    'message' => 'ارسال موفقیت آمیز بود',
                ];
            default:
                return [
                    'error' => true,
                    'message' => $res_data,
                ];
        }
    }
}
