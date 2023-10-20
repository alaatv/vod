<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ForrestService
{
    public function __construct(private Client $client)
    {
    }

    public function getTree(array $data)
    {
        $response = $this->client->get(config('services.forrest.server').'/api/v1/tree', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'query' => [
                'type' => $data['type'] ?? null,
                'multi-type' => $data['multi-type'] ?? null,
                'with-paginate' => $data['with-paginate'] ?? null,
            ]
        ]);
        return $this->getBody($response);
    }

    private function getBody($response)
    {
        return json_decode($response->getBody()->getContents());
    }

    public function getTreeByGrid($grid)
    {
        try {
            $response = $this->client->get(config('services.forrest.server').'/api/v1/tree/'.$grid, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);
            return $this->getBody($response);
        } catch (RequestException $exception) {
            return false;
        }
    }

    public function storeTree($data)
    {
        try {
            $response = $this->client->post(config('services.forrest.server').'/api/v1/tree', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($data),
            ]);
            return $this->getBody($response);
        } catch (Exception $exception) {
            return $exception;
        }
    }

    public function updateTree($grid, $data)
    {
        $response = $this->client->put(config('services.forrest.server').'/api/v1/tree/'.$grid, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'body' => json_encode($data),
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public function getTags($title)
    {
        $response = $this->client->get(config('services.forrest.server').'/api/v1/tree/search', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'query' => [
                'title' => $title
            ]
        ]);
        return $this->getBody($response);
    }
}
