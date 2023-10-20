<?php


namespace App\Classes;

use App\Collection\ContentCollection;
use App\Models\Content;
use App\Traits\APIRequestCommon;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SatraSyncer
{
    use APIRequestCommon;

    public const SAVE_API_URL = 'https://analytics.sapra.ir/api/media/save';
    public const UPDATE_API_URL = 'https://analytics.sapra.ir/api/media/update';
    public const TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjBmYjNhYzUwNWY3MjRiMWRmNTQ1ZWY2MzU0ODdhZWU1ODkzYzhhYjMzMDE2NTFjNWY0MWJlNDU0ZDhkY2M0OTNjMDQ2OGUzNzk3MjYxN2QwIn0.eyJhdWQiOiIxIiwianRpIjoiMGZiM2FjNTA1ZjcyNGIxZGY1NDVlZjYzNTQ4N2FlZTU4OTNjOGFiMzMwMTY1MWM1ZjQxYmU0NTRkOGRjYzQ5M2MwNDY4ZTM3OTcyNjE3ZDAiLCJpYXQiOjE1NzM2MzQzODIsIm5iZiI6MTU3MzYzNDM4MiwiZXhwIjoxNjA1MjU2NzgyLCJzdWIiOiI0NiIsInNjb3BlcyI6W119.DfqBRFVKJoQJ22MNpCHQZ06edgvu1-J3trKDxv2KNkJCTZaim9xUFLaFb46_xlLmMqSdBCjYejoCrI2zI95hcM445sgkG-KxCh2H084XrDNS8hXFDYULrx5hG6vQxrmR3Hd-SoHvTnLN1oR3bvn-Fnv3vTB4FAUD8Ply3WHXFuW6hHvWtVJXgVMAO7FrJxeIMolR-qzqj0LItY3ZBf6l6GpaD6P9tKGIb626p8D8g2WZxI8aaeu5r7jD-aT6-g4v13bzrfZvjWcX8B_YI7lm6jtF4M_zRX4-ICjNJ7FZHiDtRlRbtSpF5gDgu_nTZPegBdFg1EDBjNE4XNJ2-0Iy_XsLQcoUT4OJ2SqKQ5Ra32AgCX_kIgwMygQQ6NsfduNaSBKab4NBDFetogCAkiVRcufUm0XenNPXHkF0h0eRDt-ZgJeP_lWJOPccjGtjiaLzpObx1YC7sDpL0Er1IhLBReGMG4CG6tAuWDuxhQgx32SfFASlubUcm8n2Iz-yNIHuGLCM59yXh_qznyJkGaZ-Me-U6hFDjvN1R3EyfhAoAMkmAAJopLdfbNkNdqmDgPrti0BNgiUtodtK_poX5UbqbxZ7zLMFDJug6GIIpqLTxF2I4lD4nsAkmMJ1CkwPTUZVYRu1wXo6ws8oah23UI53rfp3TMWpyPOTVwp1CPUf6Ig';
    public const REQUEST_HEADERS = [
        'Authorization' => 'Bearer '.self::TOKEN,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
        'X-Requested-With' => 'XMLHttpRequest',
    ];

    /**
     * @param  Content  $content
     *
     * @return bool
     */
    public function insertContentInfo(Content $content): bool
    {
        $validSince = $content->ValidSince_Jalali(false);
        $createdAt = $content->CreatedAt_Jalali();
        $parameters = [
            'domain' => 'alaatv.com',
            'properties' => [
                'created_at' => isset($validSince) ? $validSince : $createdAt,
                'id' => $content->id,
                'content_id' => $content->id,
                'url' => $content->url,
                'view' => 0,
                'title' => (!is_null($content->name) && strlen($content->name) > 0) ? $content->name : 'جلسه '.$content->order,
            ],
        ];

        $res = $this->sendRequest(self::SAVE_API_URL, 'POST', $parameters, self::REQUEST_HEADERS);
        if ($res['statusCode'] == Response::HTTP_OK) {
            Log::info('SatraSyncer:insertContentInfo:successful:contentId:'.$content->id);
            return true;
        }

        Log::info('SatraSyncer:insertContentInfo:failed:contentId:'.$content->id);
        return false;

    }

    /**
     * @param  Content  $content
     *
     * @return bool
     */
    public function updateContentInfo(Content $content): bool
    {
        $validSince = $content->ValidSince_Jalali(false);
        $createdAt = $content->CreatedAt_Jalali();
        $parameters = [
            'domain' => 'alaatv.com',
            'media_items' => [
                [
                    'id' => $content->id,
                    'created_at' => isset($validSince) ? $validSince : $createdAt,
                    'url' => $content->url,
                    'view' => $this->countContentVisits($content),
                    'title' => $content->name,
                ],
            ],
        ];

        $res = $this->sendRequest(self::UPDATE_API_URL, 'POST', $parameters, self::REQUEST_HEADERS);
//        $result = json_decode( $res['result'] , true);
        if ($res['statusCode'] == Response::HTTP_OK) {
            return true;
        }

        return false;
    }

    private function countContentVisits(Content $content): int
    {

        $latest_id = Cache::tags(['satra'])
            ->remember('satra:countContentVisits:lastContentId', config('constants.CACHE_5'), function () {
                Content::latest('id')->first()->id;
            });
        $now = Carbon::now()->timestamp;
        $createdAt = $content->created_at->timestamp;

        $d = $now - $createdAt;
        $diff = $d / (3600 * 24);
        $a = ($d) / (60 * sqrt(24));

        return (int) ($a * (1 - $content->id / $latest_id + 0.1)) + (int) ($diff * 10);
    }

    /**
     * @param  ContentCollection  $contents
     *
     * @return bool
     */
    public function updateBulkContentsInfo(ContentCollection $contents): bool
    {
        $mediaItems = [];
        foreach ($contents as $content) {
            $validSince = $content->ValidSince_Jalali(false);
            $createdAt = $content->CreatedAt_Jalali();

            $mediaItems [] = [
                'id' => $content->id,
                'created_at' => isset($validSince) ? $validSince : $createdAt,
                'url' => $content->url,
                'view' => $this->countContentVisits($content),
                'title' => $content->name,
            ];
        }

        $parameters = [
            'domain' => 'alaatv.com',
            $mediaItems,
        ];

        $res = $this->sendRequest(self::UPDATE_API_URL, 'POST', $parameters, self::REQUEST_HEADERS);
//        $result = json_decode( $res['result'] , true);
        if ($res['statusCode'] == Response::HTTP_OK) {
            return true;
        }

        return false;
    }
}
