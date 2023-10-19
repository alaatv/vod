<?php

use App\Classes\Nullable;
use App\Classes\Util\Boolean as UtilBoolean;
use App\Models\PhoneNumberProvider;
use App\Models\SmsProvider;
use App\Models\Websitesetting;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Cache\CacheManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

if (!function_exists('nullable')) {
    function nullable($result, $data = []): Nullable
    {
        return new Nullable($result, $data);
    }
}

if (!function_exists('boolean')) {
    function boolean($result): UtilBoolean
    {
        return new UtilBoolean($result);
    }
}

if (!function_exists('httpResponse')) {
    function httpResponse($api = null, $view = null)
    {
        if (request()->expectsJson()) {
            return $api;
        }
        return $view;
    }
}

if (!function_exists('hasAuthenticatedUserPermission')) {
    function hasAuthenticatedUserPermission(string $permission): bool
    {
        return (Auth::check() && Auth::user()
                ->isAbleTo($permission));
    }
}

if (!function_exists('hasAuthenticatedUserRole')) {
    function hasAuthenticatedUserRole(string $role): bool
    {
        return (Auth::check() && Auth::user()->hasRole($role));
    }
}

if (!function_exists('clearHtml')) {
    function clearHtml($value): string
    {
        return Purify::clean($value, ['HTML.Allowed' => '']);
    }
}
if (!function_exists('convertTagStringToArray')) {
    function convertTagStringToArray($tagString): array
    {
        $tags = explode(',', $tagString);
        $tags = array_filter($tags);

        return $tags;
    }

}

if (!function_exists('convertArrayToTagString')) {
    function convertArrayToTagString($array): string|null
    {
        if(!is_array($array))
            return null;
        return implode(',', json_decode(json_encode($array), true));
    }

}

if (!function_exists('rankInArray')) {
    function rankInArray(array $array, $value): int
    {
        $rank = count($array);
        rsort($array);
        foreach ($array as $key => $item) {
            if ($value >= $item) {
                $rank = $key;
                break;
            }
        }
        return $rank + 1;
    }

}

if (!function_exists('getCurrentWeekDateViaDayName')) {
    function getCurrentWeekDateViaDayName($dayEnglishName): ?string
    {
        $startOfWeekDate = Carbon::now('Asia/Tehran')->startOfWeek(Carbon::SATURDAY);
        if ($dayEnglishName == 'saturday') {
            $date = $startOfWeekDate->toDateString();
        } else if ($dayEnglishName == 'sunday') {
            $date = $startOfWeekDate->addDay()->toDateString();
        } else if ($dayEnglishName == 'monday') {
            $date = $startOfWeekDate->addDays(2)->toDateString();
        } else if ($dayEnglishName == 'tuesday') {
            $date = $startOfWeekDate->addDays(3)->toDateString();
        } else if ($dayEnglishName == 'wednesday') {
            $date = $startOfWeekDate->addDays(4)->toDateString();
        } else if ($dayEnglishName == 'thursday') {
            $date = $startOfWeekDate->addDays(5)->toDateString();
        } else if ($dayEnglishName == 'friday') {
            $date = $startOfWeekDate->addDays(6)->toDateString();
        }
        return (isset($date)) ? $date : null;
    }
}

if (!function_exists('alaaSetting')) {
    /**
     * Get / set the specified cache value.
     *
     * If an array is passed, we'll assume you want to put to the cache.
     *
     * @param dynamic  key|key,default|data,expiration|null
     *
     * @return mixed|CacheManager
     *
     * @throws Exception
     */
    function alaaSetting()
    {
        return app(Websitesetting::class);
    }
}

if (!function_exists('convertRedirectUrlToApiVersion')) {
    function convertRedirectUrlToApiVersion(string $url, string $apiVersion = '1')
    {
        $url = parse_url($url);

        return url('/api/v' . $apiVersion . Arr::get($url , 'path'));
    }
}

if (!function_exists('pureHTML')) {
    function pureHTML(string $text)
    {
        return Purify::clean($text, ['HTML.Allowed' => 'div,b,a[href]']);
    }
}

if (!function_exists('generateSecurePathHash')) {
    function generateSecurePathHash($expires, $client_IP, $secret, $url)
    {
        $str = $expires . $url . $client_IP .' '. $secret;
        $str = base64_encode(md5($str, true));

        $str = str_replace('+', '-', $str);
        $str = str_replace('/', '_', $str);
        $str = str_replace('=', '', $str);

        return $str;
    }
}

if (!function_exists('getSecureUrl')) {
    /**
     * @param             $url
     *
     * @param string|null $download
     *
     * @return string
     */
    function getSecureUrl($url, ?int $download): string
    {
        return (isset($download) && $download)? $url . '?download=1' : $url ;
    }
}

if (!function_exists('urlAvailable')) {
    // check for available gateway bank urls
    // return available banks if only non is available return Zarinpal ( ˘︹˘ )
    function urlAvailable($gateways)
    {

        $availableGateways = [];
        $availableGateways = Cache::remember('available-bank-gateway-urls', config('constants.CACHE_1'),
            function () use ($gateways, $availableGateways) {
                foreach ($gateways as $gateway) {
                    $url = $gateway->url;
                    if (in_array($gateway->id,
                        [Transactiongateway::GATE_WAY_SAMAN_ALAA_ID, Transactiongateway::GATE_WAY_SAMAN_SOALAA_ID])) {
                        $availableGateways[$gateway->name] = $gateway;
                        continue;
                    }
                    try {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                        $rt   = curl_exec($ch);
                        $info = curl_getinfo($ch);

                        if ($info['http_code'] >= 200 and $info['http_code'] < 400) {
                            $availableGateways[$gateway->name] = $gateway;
                        }
                    } catch (Exception $e) {
                        Log::error('Error in helper method urlAvailable on pinging gateway : ' . $gateway->name . ' - ' . $e->getFile() . ' - ' . $e->getLine());
                    }
                }
//                if (count($availableGateways) > 1) {
//                    foreach ($availableGateways as $key => $gateway) {
//                        if ($key == 'zarinpal') {
//                            unset($availableGateways[$key]);
//                        }
//                    }
//                }
                return $availableGateways;
            });
        return $availableGateways;
    }
}

if (!function_exists('myAbort')) {
    /**
     * @param int $statusCode
     * @param string $message
     *
     * @param array $errors
     *
     * @return JsonResponse
     */
    function myAbort(int $statusCode , string $message, array $errors = null): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}

if (!function_exists('isFileExists')) {
    /**
     * @param string $url
     *
     * @return bool
     */
    function isFileExists(string $url): bool
    {
        $client = new Client();

        try {
            $client->head($url);
            return true;
        } catch (ClientException $e) {
            return false;
        }
    }
}

if (!function_exists('strip_punctuations')) {
    function strip_punctuations($str)
    {
        if ($str == '') {
            return '';
        }
        // edit as needed
        $punctuations = [
            '"',
            "'",
            '’',
            '˝',
            '„',
            '`',
            '.',
            ',',
            ';',
            ':',
            '+',
            '±',
            '-',
            '_',
            '=',
            '(',
            ')',
            '[',
            ']',
            '<',
            '>',
            '{',
            '}',
            '/',
            '\\',
            '|',
            '?',
            '!',
            '@',
            '#',
            '%',
            '^',
            '&',
            '§',
            '$',
            '¢',
            '£',
            '€',
            '¥',
            '₣',
            '฿',
            '*',
            '~',
            '。',
            '，',
            '、',
            '；',
            '：',
            '？',
            '！',
            '…',
            '—',
            '·',
            'ˉ',
            'ˇ',
            '¨',
            '‘',
            '’',
            '“',
            '”',
            '々',
            '～',
            '‖',
            '∶',
            '＂',
            '＇',
            '｀',
            '｜',
            '〃',
            '〔',
            '〕',
            '〈',
            '〉',
            '《',
            '》',
            '「',
            '」',
            '『',
            '』',
            '．',
            '〖',
            '〗',
            '【',
            '】',
            '（',
            '）',
            '［',
            '］',
            '｛',
            '｝',
            '／',
            '“',
            '”',
        ];
        $str = str_replace($punctuations, ' ', $str);

        return preg_replace('/\s\s+/', ' ', $str);
    }
}

if (!function_exists('appendRequestParameters')) {
    function appendRequestParameters(string $url, $request): string
    {
        $parametersString = '';
        foreach ($request->all() as $key => $requestItem) {
            if (is_array($requestItem)) {
                foreach ($requestItem as $item) {
                    if (is_string($item)) {
                        $parametersString = $parametersString . '&' . $key . '[]=' . $item;
                    }
                }
            }

            if (is_string($requestItem)) {
                $parametersString = $parametersString . '&' . $key . '=' . $requestItem;
            }
        }

        if (strlen($parametersString) > 0) {
            $url = $url . '?' . $parametersString;
        }

        return $url;
    }
}

if (!function_exists('pickAdapterDisk')) {
    function pickAdapterDisk(string $sftpDisk, $cdnDisk): string
    {
        return $sftpDisk;
//        return (config('app.env') == 'production') ? $cdnDisk : $sftpDisk;
    }
}

if (!function_exists('url_exists')) {
    function url_exists($url)
    {
        $headers = get_headers($url);
        return (bool)stripos($headers[0], '200 OK');
    }
}
if (!function_exists('url_get_size')) {
    function url_get_size($url): int
    {
        if (!url_exists($url)) {
            return 0;
        }
//        Log::channel('debug')->debug("url_get_size : url :". $url);
        $data = get_headers($url, true);
        return isset($data['Content-Length']) ? (int)$data['Content-Length'] : 0;
    }
}
if (!function_exists('file_size_formatter')) {
    function file_size_formatter(?int $size): ?string
    {
        if (is_null($size)) {
            return null;
        }
        switch ($size) {
            case $size < 1024:
                return $size . ' B';
            case $size < 1048576:
                return round($size / 1024, 2) . ' KB';
            case $size < 1073741824:
                return round($size / 1048576, 2) . ' MB';
            case $size < 1099511627776:
                return round($size / 1073741824, 2) . ' GB';
            default:
                return round($size / 1099511627776, 2) . ' TB';
        }
    }
}
if (!function_exists('getVideoLength')) {
    function getVideoLength($url)
    {
        if (!url_exists(urldecode($url))) {
            return null;
        }
        $command =
            "ffprobe -v error -hide_banner -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 -i \"" . urldecode($url) . "\"";
//        Log::channel('debug')->debug($command);
        try {
            $videoLength = (int)exec($command);
//            Log::channel('debug')->debug("l:" .$videoLength);
        } catch (Exception $exception) {
            Log::error('getVideoLength: ' . $url . ' - ' . $exception->getMessage());
        }
        return $videoLength;
    }
}

if (!function_exists('getFileSize')) {
    function getFileSize($path)
    {
        if (!file_exists(urldecode($path))) {
            return null;
        }
        $fileSizeKB = filesize(urldecode($path)) / 1024;
        return (int)$fileSizeKB;
    }
}

if (!function_exists('countPDFPages')) {
    function countPDFPages($path)
    {
        if (!file_exists(urldecode($path))) {
            return null;
        }
        $pdftext = file_get_contents(urldecode($path));
        $numOfPages = preg_match_all('/\/Page\W/', $pdftext, $dummy);
        return $numOfPages;
    }
}

if (!function_exists('determineSMSOperator')) {
    function determineSMSOperator(string $mobile): string|Exception
    {
        try {
            $operator = determineUserMobileOperator($mobile);
            return $operator?->provider?->number ??
                   SmsProvider::filter(['enable' => true, 'defaults' => true])->first()->number ??
                   throw new Exception('Provider Not Defined');
        }catch (Exception $e)
        {
            throw new Exception('Provider Not Defined');
        }
    }
}

if (!function_exists('determineUserMobileOperator')) {
    function determineUserMobileOperator(string $mobile): PhoneNumberProvider|null
    {
        //Forcing operator 1000 to all messages
        return PhoneNumberProvider::whereId(3)->first();

        foreach (PhoneNumberProvider::all() as $operator) {
            if (preg_match($operator->pattern, $mobile)) {
                return $operator;
            }
        }

        return null;
    }
}

if (!function_exists('recheckSentSmsStatusTime')) {
    function recheckSentSmsStatusTime(): Carbon
    {
        return now()->addMinutes(config('services.medianaSMS.MEDIANA_RECHECK_SEND_MESSAGE_STATUS_INTERVAL'));
    }
}

if (!function_exists('resendUnsuccessfulMessageTime')) {
    function resendUnsuccessfulMessageTime(): Carbon
    {
        return now()->addMinutes(config('services.medianaSMS.MEDIANA_RESEND_UNSUCCESSFUL_MESSAGE_INTERVAL'));
    }
}

if (!function_exists('baseTelNo')) {
    function baseTelNo($mobile): string
    {
        /*
        Tel No examples:

                 9129999999,
               0 9129999999,
              98 9129999999,
             +98 9129999999,
            0098 9129999999,

                 2162013,
               0 2162013,
              98 2162013,
             +98 2162013,
            0098 2162013,

                 100062013,
              98 100062013,
             +98 100062013,
            0098 100062013,
         */

        $mobile = str_replace('+', '', strval(intval($mobile)));
        if (substr($mobile, 0, 2) == '98') {
            $mobile = substr($mobile, 2);
        }
        return $mobile;
    }
}

if (!function_exists('reformatToUseInSync')) {
    function reformatToUseInSync(array $inputArray): array
    {
        $outputArray = [];
        foreach ($inputArray as $key => $value) {
            $subParams = [];
            foreach ($value as $k => $v) {
                if ($k != 'id') {
                    $subParams[$k] = $v;
                }
            }
            $outputArray[$value['id']] = $subParams;
        }
        return $outputArray;
    }
}

if (!function_exists('ticketDepartmentTagRedisUrl')) {
    function ticketDepartmentTagRedisUrl(int $ticketDepartmentId): string
    {
        return config('constants.TAG_API_URL') . 'id/ticketDepartment/' . $ticketDepartmentId;
    }
}

if (!function_exists('diffInPeriod')) {
    function diffInPeriod($startDate, $endDate, $carbonDiffMethodName): string
    {
        $startDate = Carbon::parse($startDate)->format('Y-m-d H:i:s');
        $startDate = Carbon::createFromDate($startDate);
        $endDate   = Carbon::parse($endDate)->format('Y-m-d H:i:s');

        return $startDate->{$carbonDiffMethodName}($endDate);
    }
}

if (!function_exists('diffInSeconds')) {
    function diffInSeconds($startDate, $endDate): string
    {
        return diffInPeriod($startDate, $endDate, __FUNCTION__);
    }
}

if (!function_exists('diffInMinutes')) {
    function diffInMinutes($startDate, $endDate): string
    {
        return diffInPeriod($startDate, $endDate, __FUNCTION__);
    }
}

if (!function_exists('diffInHours')) {
    function diffInHours($startDate, $endDate): string
    {
        return diffInPeriod($startDate, $endDate, __FUNCTION__);
    }
}

if (!function_exists('isDevelopmentMode')) {
    function isDevelopmentMode(): bool
    {
        return config('app.env') == 'local';
    }
}

if (! function_exists('appUrlRoute')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  array|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function appUrlRoute($name, $parameters = [], $absolute = true): string
    {
        $url = route($name, $parameters, $absolute);

        $parseUrl = parse_url($url);
        $url =  config('constants.APP_URL') . Arr::get($parseUrl, 'path') ;
        $query = Arr::get($parseUrl, 'query') ;
        if(isset($query))
        {
            $url = $url .  '?' . $query ;
        }

        return $url ;
    }
}

if (!function_exists('secondsToHumanFormat')) {
    /**
     * @param int|null $seconds
     * @return string|null
     */
    function secondsToHumanFormat(?int $seconds): ?string
    {
        if (!isset($seconds)) {
            return null;
        }

        // The number of seconds of 1 day (24 Hours).
        $oneDaySeconds = 86400;

        $result = gmdate('H:i:s', $seconds % $oneDaySeconds);
        $days = floor($seconds / $oneDaySeconds);
        if ($days >= 1 || $seconds == $oneDaySeconds) {
            $result = explode(':', $result);
            $result = implode(':', [($days * 24) + $result[0], $result[1], $result[2]]);
        }
        return $result;
    }
}

if (!function_exists('nestedArraySearchWithKey')) {
    /**
     * @param array $array
     * @param string $key
     * @param string $value
     * @return array|null
     */
    function nestedArraySearchWithKey(array $array, string $value, string $key): ?array
    {
        $arraySearchedValues = array_column($array, $key);
        if (!empty($arraySearchedValues)) {
            $index = array_search($value, $arraySearchedValues);
            if ($index !== false) {
                $arrayKey = array_keys($array)[$index];
                return $array[$arrayKey];
            }
        }

        return null;
    }
}

if (!function_exists('nestedArraySearchValueByAnotherField')) {
    /**
     * @param array $array
     * @param string $searchValue
     * @param string $searchKey
     * @param string $returnFieldKey
     * @return mixed
     */
    function nestedArraySearchValueByAnotherField(array $array, string $searchValue, string $searchKey, string $returnFieldKey): mixed
    {
        $searchedArray = nestedArraySearchWithKey($array, $searchValue, $searchKey);
        if (!is_null($searchedArray)) {
            return Arr::get($searchedArray, $returnFieldKey);
        }

        return null;
    }
}

if (!function_exists('searchArrayToAnotherArray')) {
    /**
     * @param array $array1
     * @param array $array2
     * @return bool
     */
    function searchArrayToAnotherArray(array $array1, array $array2): bool
    {
        return count(array_intersect($array1, $array2)) === count($array1);
    }
}

if (!function_exists('standardTelNo')) {
    function standardTelNo(string $telNo): string
    {
        return '0' . baseTelNo($telNo);
    }
}

if (!function_exists('standardNationCode')) {
    function standardNationCode(string $nationalCode): string
    {
        return str_pad($nationalCode, 10, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('removeQueryFromUrl')) {
    function removeQueryFromUrl(string $url): string
    {
        $parseUrl = parse_url($url);
        return $parseUrl['scheme'] . '://' . $parseUrl['host'] . $parseUrl['path'];
    }
}

if (!function_exists('convertBaseUrlToAppUrl')) {
    function convertBaseUrlToAppUrl(string $url): string
    {
        $parseUrl = parse_url($url);
        return config('app.url') . $parseUrl['path'] . '?' . $parseUrl['query'];
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($n)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyz';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}

if (!function_exists('randomNumber')) {
    function randomNumber($length)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }
}


if (!function_exists('defaultHeader')) {
    function defaultHeader()
    {
        return [
            'Accept' => 'application/json',
            'service-id' => config('constants.APP_IDS.ALAA.KEY'),
        ];
    }
}

if (!function_exists('array_filter_collapse')) {
    function array_filter_collapse(array $arr, callable $callback): array
    {
        return array_collapse(array_filter($arr, $callback));
    }
}

if (!function_exists('arrayInsert')) {
    /**
     * @param array $array
     * @param int|string $position
     * @param mixed $insert
     */
    function arrayInsert(&$array, $position, $insert)
    {
        if (is_int($position)) {
            array_splice($array, $position, 0, $insert);
        } else {
            $pos = array_search($position, array_keys($array));
            $array = array_merge(
                array_slice($array, 0, $pos),
                $insert,
                array_slice($array, $pos)
            );
        }
    }
}

if (!function_exists('mobileValidation')) {
    function mobileValidation($mobile): bool
    {
        return preg_match('/^(\+98|98|0)9\d{9}$/', $mobile);
    }
}

if (!function_exists('nationalCodeValidation')) {
    function nationalCodeValidation($value): bool
    {
        $arrCode = str_split($value);
        foreach ($arrCode as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }
        if (count(array_unique($arrCode)) == 1 || count($arrCode) != 10) {
            return false;
        }
        $a = $arrCode[9];
        $b = 0;
        for ($i = 0; $i < 9; $i++) {
            $b += $arrCode[$i] * (10 - $i);
        }
        $c = $b % 11;
        if ($a == $c && $a < 2 || $a == 11 - $c) {
            return true;
        }
        return false;
    }

    if (!function_exists('makeRandomOnlyAlphabeticalString')) {
        function makeRandomOnlyAlphabeticalString($length)
        {
            $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
            shuffle($seed);
            $rand = '';
            foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];
            return $rand;
        }
    }
}
