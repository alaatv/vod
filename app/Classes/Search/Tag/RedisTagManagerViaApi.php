<?php
/**
 * Created by PhpStorm.
 * User: sohrab
 * Date: 2018-09-10
 * Time: 16:05
 */

namespace App\Classes\Search\Tag;

use App\Traits\APIRequestCommon;
use Illuminate\Http\Response;
use LogicException;

abstract class RedisTagManagerViaApi implements TaggingInterface
{
    use APIRequestCommon;

    protected $bucket;

    protected $apiUrl;

    protected $limit_PerPage;

    protected $limit_WithScores;

    protected $limit_PageNum;

    /**
     * RedisTagViaApi constructor.
     *
     */
    public function __construct()
    {
        $this->apiUrl = config('constants.TAG_API_URL');
        if (!isset($this->bucket)) {
            throw new LogicException(get_class($this).' must have a $bucket');
        }
    }

    public function setTags($taggableId, array $tags, $score = 0)
    {
        $url = $this->apiUrl.'id/'.$this->bucket.'/'.$taggableId;
        $method = 'PUT';

        $params = [
            'tags' => json_encode($tags, JSON_UNESCAPED_UNICODE),

        ];
        if (isset($score)) {
            $params['score'] = $score;
        }
        $response = $this->sendRequest($url, $method, $params);
        if ($response['statusCode'] == Response::HTTP_OK) {
            //TODO:// Redis Response
        }
    }

    /**
     * @param $taggableId
     *
     * @return array
     */
    public function getTags($taggableId): array
    {
        $url = $this->apiUrl.'id/'.$this->bucket.'/'.$taggableId;
        $method = 'GET';
        $response = $this->sendRequest($url, $method);

        if ($response['statusCode'] == Response::HTTP_OK) {
            $result = json_decode($response['result']);
            $tags = $result->data->tags;
        } else {
            $tags = [];
        }

        return $tags;
    }

    /**
     * @param  array  $tags
     *
     * @return array
     */
    public function getTaggable(array $tags): array
    {
        $tags = $this->getStrTags($tags);
        $url = $this->apiUrl.'tags/'.$this->bucket.'?tags='.$tags.'&'.$this->getOptions();
        $method = 'GET';
        $response = $this->sendRequest($url, $method);

        if ($response['statusCode'] != Response::HTTP_OK) {
            return [-1, [],];
        }
        $result = json_decode($response['result']);
        $total_items_db = $result->data->total_items_db;
        $arrayOfId = [];
        foreach ($result->data->items as $item) {
            $arrayOfId[] = $item->id;
        }

        return [
            $total_items_db,
            $arrayOfId,
        ];
    }

    protected function getStrTags(array $tags)
    {
        $strTags = implode('","', $tags);
        $strTags = "[\"$strTags\"]";

        return $strTags;
    }

    protected function getOptions()
    {
        $options = 'withscores='.(int) $this->limit_WithScores;
        $options .= '&limit='.(int) $this->limit_PerPage;
        $options .= '&offset='.$this->getOffset();

        return $options;
    }

    /**
     * @return float|int
     */
    protected function getOffset()
    {
        return (int) $this->limit_PerPage * ((int) $this->limit_PageNum - 1);
    }

    public function removeTags($taggableId)
    {
        $url = $this->apiUrl.'id/'.$this->bucket.'/'.$taggableId;
        $method = 'DELETE';

        $response = $this->sendRequest($url, $method);
        if ($response['statusCode'] == Response::HTTP_OK) {
            //TODO:// Redis Response
        }
    }

    /**
     * @param  mixed  $limit_PerPage
     *
     * @return RedisTagManagerViaApi
     */
    public function setLimitPerPage($limit_PerPage)
    {
        $this->limit_PerPage = $limit_PerPage;

        return $this;
    }

    /**
     * @param  mixed  $limit_PageNum
     *
     * @return RedisTagManagerViaApi
     */
    public function setLimitPageNum($limit_PageNum)
    {
        $this->limit_PageNum = $limit_PageNum;

        return $this;
    }

    /**
     * @param  mixed  $limit_WithScores
     *
     * @return RedisTagManagerViaApi
     */
    public function setLimitWithScores($limit_WithScores)
    {
        $this->limit_WithScores = $limit_WithScores;

        return $this;
    }
}
