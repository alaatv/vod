<?php


namespace App\Classes\Marketing\Yektanet;


use App\Models\Product;
use App\Traits\APIRequestCommon;
use GuzzleHttp\Client as HttpClient;

class Yektanet
{
    use APIRequestCommon;

    public const POST_METHOD = 'post';

    public const HEADERS = [
        'Authorization' => 'Token 187ce5f3da02185b7c8351b159d35d370ef26c53',
        'Content-Type' => 'application/json',
    ];

    public const MODIFY_API_URL = 'https://prod.yektanet.com/api/v1/users/products/modify/';

    public function sendSingleProduct(Product $product): array
    {
        $productData = ['products' => [$this->createSingleYektanetFormat($product)]];

        $http = new HttpClient();

        $response = $http->request('POST', self::MODIFY_API_URL, ['json' => $productData, 'headers' => self::HEADERS]);

        return [
            'statusCode' => $response->getStatusCode(),
            'result' => $response->getBody()->getContents(),
        ];

//        return $this->sendRequest(self::MODIFY_API_URL, self::POST_METHOD, $productData, self::HEADERS);
    }

    private function createSingleYektanetFormat(Product $product): array
    {
        $availability = $product->isAvailableForAds();

        return [
            'title' => $product->name,
            'sku' => $product->id,
            'price' => $product->basePrice,
            'discount' => $product->discount,
            'image' => $product->photo,
            'url' => $product->url,
            'available' => $availability,
            'display_permission' => $availability
        ];
    }

}
