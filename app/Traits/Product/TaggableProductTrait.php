<?php


namespace App\Traits\Product;


use Illuminate\Http\Response;

trait TaggableProductTrait
{
    public function retrievingTags()
    {
        /**
         *      Retrieving Tags
         */
        $response = $this->sendRequest(config('constants.TAG_API_URL').'id/product/'.$this->id, 'GET');

        if ($response['statusCode'] == Response::HTTP_OK) {
            $result = json_decode($response['result']);
            $tags = $result->data->tags;
        } else {
            $tags = [];
        }

        return $tags;
    }

    public function getTaggableTags()
    {
        return optional($this->tags)->tags;
    }

    public function getTaggableId(): int
    {
        return $this->id;
    }

    public function getTaggableScore()
    {
        return optional($this->created_at)->timestamp;
    }

    public function isTaggableActive(): bool
    {
        if ($this->isActive() && isset($this->tags) && !empty($this->tags->tags)) {
            return true;
        }

        return false;
    }
}
