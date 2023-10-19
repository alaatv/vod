<?php


namespace App\Traits\User;


use App\Collection\ContentCollection;
use Illuminate\Http\Response;

trait TaggableUserTrait
{
    public function getTaggableId()
    {
        return $this->id;
    }

    public function getTaggableScore()
    {
        return null;
    }

    public function isTaggableActive(): bool
    {
        $userContents = $this->contents;
        if (count($userContents) == 0) {
            return false;
        }

        return true;
    }

    public function getTaggableTags()
    {
        $userContents = $this->contents;

        return $this->mergeContentTags($userContents);
    }

    /**
     * @param $userContents
     *
     * @return array
     */
    private function mergeContentTags(ContentCollection $userContents): array
    {
        $tags = [];
        foreach ($userContents as $content) {
            $contentTags = optional($content->tags)->tags;
            $tags = array_merge($tags, isset($contentTags) ? $contentTags : []);
        }
        $tags = array_values(array_unique($tags));

        return $tags;
    }

    public function retrievingTags()
    {
        /**
         *      Retrieving Tags
         */
        $response = $this->sendRequest(config('constants.TAG_API_URL').'id/author/'.$this->id, 'GET');

        if ($response['statusCode'] == Response::HTTP_OK) {
            $result = json_decode($response['result']);
            $tags = $result->data->tags;
        } else {
            $tags = [];
        }

        return $tags;
    }
}
