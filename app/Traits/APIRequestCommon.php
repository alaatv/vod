<?php namespace App\Traits;


use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

trait APIRequestCommon
{
    public function update3AUserRequest(User $user, array $params): bool
    {
        $path = "/user/{$user->id}";
        $method = 'PUT';
        return $this->sendRequestTo3A($user, $path, $method, $params);
    }

    private function sendRequestTo3A(
        User $user,
        string $path,
        string $method = 'GET',
        array $params = [],
        array $httpResponseStatusCodes = [Response::HTTP_OK],
        array $headers = null
    ): bool {
        $token = $user->get3AToken();

        $headers = $headers ?? [
            'Authorization' => 'Bearer '.Arr::get($token, 'access_token'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $response = $this->sendRequest(config('constants.SOALAA_API_BASE_URL').$path, $method, $params, $headers);

        if (in_array($response['statusCode'], $httpResponseStatusCodes)) {
            return true;
        }

        return false;
    }

    public function sendRequest($path, $method, $parameters = [], $headers = [])
    {
        $client = new Client();
        try {
            if (empty($headers)) {
                $res = $client->request($method, $path,
                    ['timeout' => 1, 'form_params' => $parameters, 'version' => 1.1]);
            } else {
                $res = $client->request($method, $path,
                    ['query' => $parameters, 'headers' => $headers, 'version' => 1.1]);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error('APIRequestCommon:sendRequest:'.$path);
//            throw new Exception($e->getMessage());
        }

        if (isset($res)) {
            return [
                'statusCode' => $res->getStatusCode(),
                'result' => $res->getBody()->getContents(),
            ];

        }

        return [
            'statusCode' => Response::HTTP_SERVICE_UNAVAILABLE,
            'result' => 'cURL error',
        ];

    }

    public function postToSkyroom($data, $format = 'json')
    {
        $contentType = 'application/json';

        if ($format !== 'json') {
            $contentType = 'application/x-www-form-urlencoded';
            $queryString = '';
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    $queryString .= $key.'='.$value.'&';
                }
                $data = rtrim($queryString, '&');
            }
        }

        // set request options
        $curl = curl_init('https://www.skyroom.online/skyroom/api/apikey-1911118-99-e9bf91c0fc3364ef9e3019642c365f21');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            "Content-Type: $contentType",
        ]);

        // make the request
        $response = curl_exec($curl);
        $errNo = curl_errno($curl);
        if ($errNo !== 0) {
            Log::error('error on sending request to skyroom: error number '.$errNo);
            return [
                'result' => false,
                'data' => null,
            ];
//            throw new NetworkException(curl_error($curl), $errNo);
        }

        // check HTTP status code
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($http_code !== 200) {
//            throw new HttpException('HTTP Error', $http_code);
            Log::error('error on sending request to skyroom: http code '.$http_code);
            return [
                'result' => false,
                'data' => null,
            ];
        }

        // decode JSON response
        $response = json_decode($response, true);
        if ($response === null) {
            Log::error('error on sending request to skyroom: got null data');
//            throw new JsonException('Invalid Response', json_last_error());
            return [
                'result' => true,
                'data' => null,
            ];
        }

        return [
            'result' => true,
            'data' => $response,
        ];
    }

    private function register3ARequest(User $user, string $examId)
    {
        $path = '/user/registerExam';
        $method = 'POST';
        $params = ['exam_id' => $examId];
        $httpResponseStatusCodes = [Response::HTTP_CREATED, Response::HTTP_OK];

        return $this->sendRequestTo3A($user, $path, $method, $params, $httpResponseStatusCodes);
    }
}
