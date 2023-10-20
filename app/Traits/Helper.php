<?php

namespace App\Traits;

use App\Jobs\LogSendBulkSms;
use App\Libraries\Sha1Hasher;
use App\Models\SmsBlackList;
use App\Models\SmsProvider;
use App\Repositories\SmsBlackListRepository;
use Carbon\Carbon;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use ReflectionClass;


trait Helper
{
    use IppanelCommon;

    public function timeFilterQuery(
        $list,
        $sinceDate = null,
        $tillDate = null,
        $by = 'created_at',
        $timeZoneConvert = true
    ) {
        if ($sinceDate) {
            $sinceDate = Carbon::parse($sinceDate)->setTimezone('Asia/Tehran')->format('Y-m-d').' 00:00:00';
            $sinceDate = $timeZoneConvert ? $this->timeZoneConvert($sinceDate) : $sinceDate;
            $list = $list->where($by, '>=', $sinceDate);
        }

        if ($tillDate) {
            $tillDate = Carbon::parse($tillDate)->addDay()->setTimezone('Asia/Tehran')->format('Y-m-d').' 00:00:00';
            $tillDate = $timeZoneConvert ? $this->timeZoneConvert($tillDate) : $tillDate;
            $list = $list->where($by, '<', $tillDate);
        }

        return $list;
    }

    private function timeZoneConvert($date)
    {
        return Carbon::parse($date, 'Asia/Tehran')->setTimezone('UTC');
    }

    /**
     * Update model without touching it's updated_at
     *
     * @return bool
     */
    public function updateWithoutTimestamp($args = null): bool
    {
        $this->timestamps = false;
        $flag = is_null($args) ? $this->update() : $this->update($args);
        $this->timestamps = true;

        return $flag;
    }

    public function getCacheClearUrlAttribute(): ?string
    {
        if (!auth()->check() || !auth()->user()->isAbleTo(config('constants.ENTITY_CACHE_CLEAR_ACCESS'))) {
            return null;
        }

        $className = (new ReflectionClass($this))->getShortName();
        $className = strtolower($className);
        return route('web.admin.cacheclear', ["$className" => 1, 'id' => $this->id]);
    }

    public function medianaHumanReadableResponse($response): JsonResponse
    {
        if (isset($response['error']) && $response['error']) {
            $msg = isset($response['has_response']) ? 'سامانه پیامکی پاسخ خطا برمیگرداند!' : 'شکست در استفاده از سامانه پیامکی!';
            return response()->json(['message' => $msg], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        if ($response === -1) {
            return response()->json([
                'message' => 'طی '.config('constants.MINIMUM_SMS_SENDING_INTERVAL').' روز اخیر به کاربران انتخاب شده پیامی با این مظمون فرستاده شده',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        $smsCredit = $this->medianaGetCredit();
        return response()->json($smsCredit);
    }

    public function medianaGetCredit()
    {
        $client = $this->ippanelClientObj();
        return $client->getCredit();
    }

    public function getProviderFromRequest(array $params): ?string
    {
        return Arr::get($params, 'from') ?? Arr::get($params, 'smsProviderNumber') ?? null;
    }

    private function medianaSendBulkSMS(
        string $message,
        array $mobiles,
        $smsNumber,
        bool $filterBlackList = true,
        $users = null
    ) {
        $mobiles = $filterBlackList ? $this->diffWithBlackList($mobiles) : $mobiles;
        if (empty($mobiles)) {
            throw new InvalidArgumentException('no mobile number found');
        }
        if ($users) {
            $users->load('sms');
            $sha1 = new Sha1Hasher();
            foreach ($users as $user) {
                $smsSentToUser = $user->sms
                    ->where('created_at', '<', now())
                    ->where(
                        'created_at',
                        '>',
                        now()->subDays(config('constants.MINIMUM_SMS_SENDING_INTERVAL'))
                    );
                foreach ($smsSentToUser as $item) {
                    if (
                        $sha1->check(
                            $message."\n".SmsBlackList::DISABLE_SMS_WORDS[0],
                            $item->sms->sha1
                        )
                    ) {
                        unset($mobiles[array_search($user->mobile, $mobiles)]);
                    }
                }
            }
            if (empty($mobiles)) {
                return -1;
            }
        }
        $smsInfo = [
            'message' => $message,
            'to' => $mobiles,
            'from' => $smsNumber,
        ];
        $response = $this->medianaSendSMS($smsInfo);
        LogSendBulkSms::dispatch($response, auth()->user());
        return $response;
    }

    private function diffWithBlackList(array $mobiles): array
    {
        $blacklist = SmsBlackListRepository::getBlockedList()
            ?->get()
            ?->pluck('mobile')
            ?->toArray();
        if (!$blacklist) {
            return $mobiles;
        }

        return array_diff($mobiles, $blacklist);
    }

    /**
     * Sending SMS request to Mediana SMS Panel
     *
     * @param  array  $params
     * @return array|array[]
     */
    public function medianaSendSMS(array $params)
    {
        if (!isset($params['to']) || !count($params['to'])) {
            return $this->machineReadableResponse('', 'No receiver determined', null, null, null, true, false);
        }

        $message = $params['message']."\n".SmsBlackList::DISABLE_SMS_WORDS[0];
        return $this->send($params, $message);
    }

    protected function machineReadableResponse(
        $mobiles,
        string $message,
        array|string $operator,
        $response,
        bool $has_response,
        bool $has_error,
        bool $has_result = true
    ): array {
        $returnData = $this->setResultKey($has_result, $mobiles, $operator);
        $returnData['message'] = $message;
        $returnData['error'] = $has_error;
        $returnData['has_response'] = $has_response;
        $returnData['response'] = $response;

        return $returnData;
    }

    protected function setResultKey(bool $has_result, $mobiles, $operator): array
    {
        if (!$has_result) {
            return ['result' => null];
        }

        return [
            'result' => [
                'to' => $mobiles,
                'provider_id' => $this->setProviderId($operator),
            ]
        ];
    }

    protected function setProviderId(string $operator)
    {
        return SmsProvider::filter(['number' => $operator, 'enable' => true])->first()?->id;
    }

    protected function send($params, $message)
    {
        $responses = [];   // todo: it must be an array and add all response to this
        // todo: then we should refactor representation layer base this new structure
        $recipientGroups = $this->SeparationMobilesBaseOperator($params);

        foreach ($recipientGroups as $provider => $recipients) {
            if (empty($recipients)) {
                $responses = $this->emptyResponse($provider, $params['message']);
            }
            $responses = $this->trySendResponse($provider, $recipients, $message);
        }

        return $responses;
    }

    protected function SeparationMobilesBaseOperator($params)
    {
        if (isset($params['from']) && !is_null($params['from'])) {
            return [$params['from'] => array_map(fn($mobile) => trim($mobile), $params['to'])];
        }

        $recipients = [];
        $defaultProviders = SmsProvider::filter(['defaults' => true, 'enable' => true])->get();

        if ($defaultProviders->isEmpty()) {
            return $recipients;
        }

        foreach ($params['to'] as $recipient) {
            $mobile = '98'.baseTelNo($recipient);
            $operator = determineUserMobileOperator($recipient);
            if (is_null($operator)) {
                Log::channel('debug')->warning("No SMS provider found for mobile number {$recipient}");
                continue;
            }
            foreach ($defaultProviders as $provider) {
                if ($operator->provider->is($provider)) {
                    $recipients[$provider->number][] = $mobile;
                }
            }
        }

        return $recipients;
    }

    protected function emptyResponse($from, $message)
    {
        Log::channel('medianaIppanelApi')->error('Api response error - Sending bulk sms. Error message: No recipients');
        return $this->machineReadableResponse(
            [],
            $message,
            $from,
            null,
            true,
            true);
    }

    protected function trySendResponse($from, $recipients, $message)
    {
        try {
            $from = '98'.baseTelNo($from);
            $response = $this->sendMessage($from, $recipients, $message);
            return $this->machineReadableResponse($recipients, $message, $from, $response, true, false);

        } catch (Exception $e) {

            Log::channel('medianaIppanelApi')->error('Api fail - Sending bulk sms. Exception message: '.json_encode($e));
            return $this->machineReadableResponse($recipients, $message, $from, $e, false, true);
        }
    }
}
