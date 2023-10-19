<?php

namespace App\Helpers;

use Exception;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class GuzzleRequest
{
    public static function send(string $method, string $uri, array $form_params = [], array $headers = [])
    {
        try {
            $client = new Client();
            $response = $client->request(
                $method,
                $uri,
                [
                    'headers' => $headers,
                    'form_params' => $form_params,
                ],
            );
        } catch (Exception $e) {
            if ($e->getCode() == Response::HTTP_UNPROCESSABLE_ENTITY) {
                $errors = json_decode($e->getResponse()->getBody()->getContents());
                return ['errors' => $errors->errors, 'status_code' => $e->getCode()];
            }
            return ['errors' => $e->getMessage(), 'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR];
        }
        $data = collect(json_decode($response->getBody()->getContents(), true));
        $data->put('status_code', $response->getStatusCode());
        return $data;
    }
}
