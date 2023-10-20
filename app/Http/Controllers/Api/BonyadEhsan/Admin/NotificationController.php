<?php

namespace App\Http\Controllers\Api\BonyadEhsan\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BonyadEhsan\Admin\NotificationIndexRequest;
use App\Services\MicroServiceAuthentication\BonyadServiceAuthentication;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:'.config('constants.BONYAD_EHSAN_NOTIFICATION_READ'),
            ['only' => ['read', 'readAll'],]);
    }

    public function index(NotificationIndexRequest $request)
    {
        $formParams = [
            'owner_id' => 1,
            'read' => $request->read,
            'user_id' => auth('api')->user()->id,
        ];
        return $this->send('GET', '/api/v1/service/notification', $formParams);
    }

    private function send($method, $url, $form_params)
    {
        $bonyadServiceAuthentication = new BonyadServiceAuthentication();
        $token = $bonyadServiceAuthentication->login();
        if (is_string($token)) {
            $body = [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$token
                ],
            ];
            if ($method == 'GET') {
                $body['query'] = $form_params;
            } elseif ($method == 'POST') {
                $body['form_params'] = $form_params;
            }

            try {
                $client = new Client();
                $response = $client->request(
                    $method,
                    config('services.bonyad.server').$url,
                    $body
                );
            } catch (Exception $exception) {
                $errors = json_decode($exception->getResponse()->getBody()->getContents());
                return ['errors' => $errors->message, 'status_code' => $exception->getCode()];
            }
            $data = json_decode($response->getBody(), true);
            return $data;
        }
        return $token ?? response()->json([
            'messege' => 'مشکلی در اتصال پیش آمده',
        ]);
    }

    public function read(Request $request, $id)
    {
        $formParams = [
            'user_id' => auth('api')->user()->id,
        ];
        return $this->send('POST', '/api/v1/service/notification/'.$id.'/read', $formParams);
    }

    public function readAll(Request $request)
    {
        $formParams = [
            'user_id' => auth('api')->user()->id,
            'owner_id' => 1
        ];
        return $this->send('POST', '/api/v1/service/notification/readAll', $formParams);
    }
}
