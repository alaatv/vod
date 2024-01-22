<?php

namespace App\Services;

use App\Helpers\GuzzleRequest;
use App\Models\Order;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use function Sentry\captureException;

class EwanoService
{
    public static function GetToken(string $uuid)
    {

        $uri = config('constants.EWANO_WEBSERVICE') . '/services/auth/v1.0/user/login/token/' . $uuid;

        $request = GuzzleRequest::send('GET', $uri, [], [
            'clientId' => config('constants.EWANO_CLIENTID'),
            'clientSecret' => config('constants.EWANO_CLIENT_SECRET'),
        ]);

        return Arr::get($request, 'result.data');
    }

    public static function UserInquiry(string $token)
    {

        $uri = config('constants.EWANO_WEBSERVICE') . '/services/user/v1.0/profile';

        $request = GuzzleRequest::send('GET', $uri, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        return Arr::get($request, 'result.data');
    }

    public static function CreateOrder(User $user, Order $order, string $refreshToken)
    {

        $token = self::RefreshToken($user, $refreshToken);
        $uri = config('constants.EWANO_WEBSERVICE') . '/services/ecommerce/order/v1.0/thirdparty';

        $client = new Client();
        $request = $client->post(
            $uri,
            [
                'body' => json_encode([
                    'msisdn' => substr($user->mobile, 1),
                    'id' => (string)$order->getKey(),

                    'description' => $order->customerDescription ?? '',
                    'discountAmount' => $order->discount,
                    'items' => $order->orderproducts->map(fn($orderProduct) => [
                        'name' => $orderProduct->product->name,
                        'quantity' => $orderProduct->quantity,
                        'unit_price' => $orderProduct->tmp_final_cost * 10,
                    ])->toArray(),
                ]),
                'headers' => [
                    'Authorization' => 'Bearer ' . Arr::get($token, 'token'),
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $request = json_decode($request->getBody()->getContents());

        return self::Order($user, $request->result->data->id, Arr::get($token, 'token'));

    }

    public static function RefreshToken(User $user, string $refreshToken)
    {

        $uri = config('constants.EWANO_WEBSERVICE') . '/services/auth/v1.0/token/refresh/' . $refreshToken;

        $request = GuzzleRequest::send('GET', $uri, [], []);

        $user->ewanoUser()->update([
            'refresh_token' => Arr::get($request, 'result.data.refreshToken'),
        ]);

        return Arr::get($request, 'result.data');
    }

    public static function Order(User $user, string $orderId, string $token)
    {

        $uri = config('constants.EWANO_WEBSERVICE') . '/services/ecommerce/order/v1.0/' . $orderId;

        $client = new Client();
        $request = $client->get(
            $uri,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        return json_decode($request->getBody()->getContents());
    }

    public static function Pay(User $user, string $ewanoOrderId, string $refreshToken)
    {
        $token = self::RefreshToken($user, $refreshToken);
        $uri = config('constants.EWANO_WEBSERVICE') . '/services/ecommerce/order/v1.0/payment/' . $ewanoOrderId . '/wallet';
        $client = new Client();
        try {
            $request = $client->put($uri, [
                'body' => json_encode([]),
                'headers' => [
                    'Authorization' => 'Bearer ' . Arr::get($token, 'token'),
                    'Content-Type' => 'application/json',
                ],
            ]);

            return $request->getBody()->getContents();
        } catch (Exception $exception) {
            captureException($exception);

            return null;
        }
    }
}
