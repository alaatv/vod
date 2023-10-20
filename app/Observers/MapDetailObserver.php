<?php

namespace App\Observers;

use App\Classes\Search\Tag\TaggingInterface;
use App\Models\MapDetail;
use App\Traits\APIRequestCommon;
use App\Traits\RequestCommon;
use App\Traits\TaggableTrait;
use Illuminate\Support\Facades\Cache;

class MapDetailObserver
{
    private $tagging;

    use RequestCommon;
    use APIRequestCommon;
    use TaggableTrait;

    public function __construct(TaggingInterface $tagging)
    {
        $this->tagging = $tagging;
    }

    /**
     * Handle the ticket "deleted" event.
     *
     * @param  MapDetail  $mapDetail
     * @return void
     */
    public function deleted(MapDetail $mapDetail)
    {
        Cache::tags(['mapDetail_'.$mapDetail->id, 'mapDetail_search'])->flush();
    }


    public function saved(MapDetail $mapDetail)
    {
        //ToDo : it is not working and needs a debug . commented temporarily and replaced with an alternatice method
//        $this->sendTagsOfTaggableToApi($mapDetail, $this->tagging);
        $itemTagsArray = $mapDetail->tags;
        $params = [
            'tags' => json_encode($itemTagsArray, JSON_UNESCAPED_UNICODE),
        ];

        $response = $this->sendRequest(config('constants.TAG_API_URL').'id/mapDetail/'.$mapDetail->id, 'PUT', $params);

        Cache::tags(['mapDetail_'.$mapDetail->id, 'mapDetail_search'])->flush();
    }
}
