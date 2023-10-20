<?php

namespace App\Traits;

use App\Jobs\RecheckSentSmsStatus;
use App\Libraries\Sha1Hasher;
use App\Models\SMS;
use App\Models\SmsDetail;
use App\Models\SmsResult;
use App\Models\SmsUser;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use IPPanel\Client;
use IPPanel\Models\Recipient;
use Throwable;


trait IppanelCommon
{
    public function sendMessage($transmitter, $recipients, $message)
    {
        $client = $this->ippanelClientObj();
        return $client->send($transmitter, $recipients, $message);
    }

    public function ippanelClientObj()
    {
        return new Client(config('services.medianaSMS.api_key'));
    }

    /**
     * @param $sms
     * @return array
     */
    public function updateSentMessageStatus($sms)
    {
        $errorResponse = null;
        try {
            $response = $this->getMessage($sms->detail->bulk_id);

            // Return bulk_id if api response is ok.
            if (isset($response->bulkId) && is_numeric($response->bulkId)) {
                $sms->detail->update([
                    'provider_number' => $response->number,
                    'provider_message' => $response->message,
                    'provider_status' => $response->status,
                    'provider_sms_type' => $response->type,
                    'provider_confirm_state' => $response->confirmState,
                    'provider_created_at' => Carbon::parse($response->createdAt)->format('Y-m-d H:i:s'),
                    'provider_sent_at' => Carbon::parse($response->sentAt)->format('Y-m-d H:i:s'),
                    'provider_recipients_count' => $response->recipientsCount,
                    'provider_valid_recipients_count' => $response->validRecipientsCount,
                    'provider_page' => $response->page,
                    'provider_cost' => $response->cost,
                    'provider_payback_cost' => $response->paybackCost,
                    'provider_description' => $response->description,
                ]);

                return [
                    'error' => false,
                    'has_response' => true,
                    'response' => $response,
                ];
            }

            // Return response message if api response has error.
            Log::channel('medianaIppanelApi')->error('Api response error - Recheck sent sms status. Error message: '.json_encode($response));
            return [
                'error' => true,
                'has_response' => true,
                'response' => $response,
            ];
        } // Notice! Please don't remove these catch statements.
        catch (Error $error) {
            // Notice! Please don't remove these catch statements.
            $errorResponse = $error;
        } catch (Exception $exception) {
            // Notice! Please don't remove these catch statements.
            $errorResponse = $exception;
        } catch (Throwable $throwable) {
            // Notice! Please don't remove these catch statements.
            $errorResponse = $throwable;
        }

        // Return fail if api fails.
        Log::channel('medianaIppanelApi')->error('Api fail - Recheck sent sms status. Exception message: '.json_encode($errorResponse));
        return [
            'error' => true,
            'has_response' => false,
            'response' => $errorResponse,
        ];
    }

    public function getMessage($bulkId)
    {
        $client = $this->ippanelClientObj();
        return $client->getMessage($bulkId);
    }

    /**
     * @param $sms
     * @return bool|Recipient[]
     */
    public function updateRecipientsStatus($sms)
    {
        try {
            // Notice: Don't change $limit amount please.
            $limit = 10;
            $client = $this->ippanelClientObj();
            $response = $client->fetchStatuses($sms->detail->bulk_id, 0, $limit);

            $fetchError = true;
            if (
                isset($response) &&
                isset($response[0]) &&
                count($response[0]) &&
                isset($response[1]) &&
                $response[1]->total > 0
            ) {
                $fetchError = false;
                $this->updateOrCreateSmsUser($response[0], $sms->id);

                $totalRecipients = $response[1]->total;
                if ($totalRecipients > $limit) {
                    $this->updateOrCreateSmsUser($response[0], $sms->id);
                    for ($i = 1; $i <= $totalRecipients / $limit; $i++) {
                        $refetchResponse = $client->fetchStatuses($sms->detail->bulk_id, $i, $limit);
                        $this->updateOrCreateSmsUser($refetchResponse[0], $sms->id);
                    }
                }
            }

            // Return ok if api response be ok.
            if (
                !$fetchError &&
                isset($refetchResponse) &&
                isset($refetchResponse[0]) &&
                count($refetchResponse[0]) &&
                isset($refetchResponse[1]) &&
                $refetchResponse[1]->total > 0
            ) {
                return [
                    'error' => false,
                    'has_response' => true,
                    'response' => $response,
                ];
            }

            // Return response message if api response has error.
            Log::channel('medianaIppanelApi')->error('Api response error - Recheck sent sms recipients status. Error message: '.json_encode($response));
            return [
                'error' => true,
                'has_response' => true,
                'response' => $response,
            ];
        } catch (Exception $e) {

            // Return fail if api fails.
            Log::channel('medianaIppanelApi')->error('Api fail - Recheck sent sms recipients status. Exception message: '.json_encode($e));
            return [
                'error' => true,
                'has_response' => false,
                'response' => $e,
            ];
        }
    }

    public function updateOrCreateSmsUser($recipients, int $smsId)
    {
        foreach ($recipients as $recipient) {
            $tel = baseTelNo($recipient->recipient);
            if (($smsUser = SmsUser::where('sms_id', $smsId)->where('mobile', 'like', "%{$tel}"))->exists()) {
                $smsUser->update([
                    'status' => $recipient->status,
                ]);
            }
            // TODO: Add SmsUser Created codes.
            // TODO: منتها باید بررسی بشه که چطور ممکنه لیست گیرندگانی که مد نظر ماست با لیست گیرندگانی که سامانه برای آنها پیامک ارسال کرده فرق میکنه
            // TODO: طبق گفته بالا پس در حقیقت در این بخش نیاز به ایجاد کردن یک سطر جدید در جدول sms_users نخواهد بود
        }
    }

    /**
     * @param  array  $recipients
     * @param  int|null  $smsProviderId
     * @param  string|null  $message
     * @param  string|null  $patternCode
     * @param  array|null  $patternData
     * @param  int|null  $bulkId
     * @param  string|null  $smsResult
     * @param  User|null  $user
     * @param  string|null  $foreign_id
     * @param  string|null  $foreign_type
     * @return mixed
     */
    public function logSentSms(
        array $recipients,
        int $smsProviderId = null,
        string $message = null,
        string $patternCode = null,
        array $patternData = null,
        int $bulkId = null,
        string $smsResult = null,
        User $user = null,
        string $foreign_id = null,
        string $foreign_type = null,
    ) {
        $sha1 = new Sha1Hasher();
        $sms = SMS::query()->create([
            'message' => $message,
            'sha1' => $sha1->make($message),
            'provider_id' => $smsProviderId,
            'sent' => true,
            'foreign_id' => $foreign_id,
            'foreign_type' => $foreign_type,
        ]);

        SmsDetail::query()->create([
            'sms_id' => $sms->id,
            'bulk_id' => $bulkId,
            'pattern_data' => isset($patternData) ? json_encode($patternData, JSON_UNESCAPED_UNICODE) : null,
            'pattern_code' => $patternCode,
            'sms_result_id' => $smsResult,
            'admin_user_id' => auth()->check() ? auth()->id() : ($user->id ?? null),
        ]);

        foreach ($recipients as $recipient) {
            $mobile = baseTelNo($recipient['mobile'] ?? $recipient);
            $params = [
                'sms_id' => $sms->id,
                'user_id' => $recipient['id'] ?? User::where('mobile', 'like', "%{$mobile}%")->first()->id ?? null,
                'mobile' => "0{$mobile}",
            ];
            try {
                //ToDo: An exception should be resolved when we have 2 accounts with the same phone number in recipient users
                SmsUser::query()->create($params);
            } catch (QueryException $e) {
                //
            }
        }

        if ($smsResult == SmsResult::DONE_ID) {
            RecheckSentSmsStatus::dispatch($sms)->delay(recheckSentSmsStatusTime());
        }

        return $sms;
    }

    public function sendPatternSmsByApi($to, $from, $patternCode, $patternData)
    {
        // Important: The recipient number must be generated as follows. Note that the Mediana ippanel document was mispronounced.
        $to = '0'.baseTelNo($to);
        $from = '+98'.baseTelNo($from);

        try {

            // Send pattern message
            $client = $this->ippanelClientObj();
            $response = $client->sendPattern($patternCode, $from, $to, $patternData);

            // Return bulk_id if api response is ok.
            if (is_numeric($response)) {
                try {
                    $messageStatus = $this->getMessage($response);
                    $message = $messageStatus->message;
                } catch (Error $e) {
                    //
                } catch (Exception $e) {
                    //
                } catch (Throwable $e) {
                    //
                }
                return [
                    'error' => false,
                    'has_response' => true,
                    'response' => $response,
                    'message' => $message ?? null,
                ];
            }

            // Return response message if api response has error.
            Log::channel('medianaIppanelApi')->error('Api response error - Sending pattern sms. Error message: '.json_encode($response));
            return [
                'error' => true,
                'has_response' => true,
                'response' => $response,
                'message' => null,
            ];

        } catch (Exception $e) {
            // Return fail if api fails.
            Log::channel('medianaIppanelApi')->error('Api fail - Sending pattern sms. Exception message: '.json_encode($e));
            return [
                'error' => true,
                'has_response' => false,
                'response' => $e,
                'message' => null,
            ];
        }
    }

    /**
     * @param  array  $response
     * @param  SMS  $sms
     * @return bool
     */
    private function isAllowedRecheckSmsStatus(array $response, SMS $sms): bool
    {
        $validDiffTime = diffInMinutes($sms->created_at,
                now('Asia/Tehran')->format('Y-m-d H:i:s')) <= config('services.medianaSMS.RECHECK_SENT_MESSAGE_STATUS_PERIOD');
        return $response['error'] && !$response['has_response'] && $validDiffTime;
    }
}
