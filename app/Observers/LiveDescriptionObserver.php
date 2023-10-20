<?php

namespace App\Observers;

use App\Classes\Search\Tag\TaggingInterface;
use App\Models\LiveDescription;
use App\Services\MicroServiceAuthentication\BonyadServiceAuthentication;
use App\Traits\APIRequestCommon;
use App\Traits\TaggableTrait;
use Cache;
use Exception;
use GuzzleHttp\Client;

class LiveDescriptionObserver
{
    use TaggableTrait;
    use APIRequestCommon;

    private $tagginh;

    public function __construct(TaggingInterface $tagging)
    {
        $this->tagging = $tagging;
    }

    public function updated(LiveDescription $liveDescription)
    {
        $this->flushCache($liveDescription);
    }

    private function flushCache(LiveDescription $liveDescription)
    {
        Cache::tags(['pinned_live_descriptions', 'livedescription', 'livedescription_'.$liveDescription->id])->flush();
    }

    public function deleted(LiveDescription $liveDescription)
    {
        $this->flushCache($liveDescription);
    }

    public function saved(LiveDescription $liveDescription)
    {
        $this->sendTagsOfTaggableToApi($liveDescription, $this->tagging);

        Cache::tags([
            'livedescription_'.$liveDescription->id,
            'live_description_search',
        ])->flush();
    }

    public function created(LiveDescription $liveDescription)
    {
        if ($liveDescription->owner == config('constants.BONYAD_OWNER')) {
            $bonyadServiceAuthentication = new BonyadServiceAuthentication();
            $token = $bonyadServiceAuthentication->login();
            try {
                $client = new Client();
                $response = $client->request(
                    'POST',
                    config('services.bonyad.server').'/api/v1/service/notification',
                    [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Authorization' => 'Bearer '.$token
                        ],
                        'form_params' => [
                            'owner_id' => 1,
                            'message_id' => 1,
                        ]
                    ]
                );
            } catch (Exception $exception) {
                $errors = json_decode($exception->getResponse()->getBody()->getContents());
            }
        }
    }

}
