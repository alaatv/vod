<?php

namespace App\Services\MicroServiceAuthentication;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use function Sentry\captureException;

class BonyadServiceAuthentication implements IBonyadServiceAuthentication
{
    public function login()
    {
        return Cache::remember('bonyad-auth-token', config('services.bonyad.token_time'), function () {
            try {
                $client = new Client();
                $response = $client->request(
                    'POST',
                    config('services.bonyad.server').'/api/v1/general/auth/login',
                    [
                        'headers' => [
                            'Accept' => 'application/json'
                        ],
                        'form_params' => [
                            'name' => config('services.bonyad.name'),
                            'password' => config('services.bonyad.password')
                        ]
                    ]
                );
            } catch (Exception $exception) {
                captureException($exception);
                $errors = json_decode($exception->getResponse()?->getBody()->getContents());
                return $errors->errors ? ['errors' => $errors->errors, 'status_code' => $exception->getCode()] : null;
            }
            $data = json_decode($response->getBody()->getContents(), true);
            $data['status_code'] = $response->getStatusCode();
            return $data['data']['authorisation']['token'];
        });
    }
}
