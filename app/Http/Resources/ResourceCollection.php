<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ResourceCollection extends \Illuminate\Http\Resources\Json\ResourceCollection
{
    /**
     * The name of the resource being collected.
     *
     * @var string
     */
    public $collects;

    /**
     * Create a new anonymous resource collection.
     *
     * @param  mixed  $resource
     * @param  string  $collects
     * @return void
     */
    public function __construct($resource, $collects)
    {
        $this->collects = $collects;

        parent::__construct($resource);
    }

    public function toJsonEncode($options = 0): string
    {
        return $this->toResponse(null)->content();
    }

    public function toResponse($request)
    {
        $response = parent::toResponse($request);
        $data = $response->getData(true);

        if (isset($data['meta'], $data['meta']['links'])) {
            unset($data['meta']['links']);
        }
        $response->setData($data);
        return $response;

    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'count' => $this->count(),
            ],
        ];
    }


}
