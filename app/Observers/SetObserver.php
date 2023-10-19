<?php

namespace App\Observers;

use App\Classes\Search\Tag\TaggingInterface;
use App\Jobs\UpdateDanaSessionName;
use App\Models\Contentset;
use App\Models\DanaProductSetTransfer;
use App\Models\DanaProductTransfer;
use App\Traits\TaggableTrait;
use Illuminate\Support\Facades\Cache;

class SetObserver
{
    private $tagging;

    use TaggableTrait;

    public function __construct(TaggingInterface $tagging)
    {
        $this->tagging = $tagging;
    }

    /**
     * Handle the product "created" event.
     *
     * @param  Contentset  $set
     *
     * @return void
     */
    public function created(Contentset $set)
    {
    }

    /**
     * Handle the product "updated" event.
     *
     * @param  Contentset  $set
     *
     * @return void
     */
    public function updated(Contentset $set)
    {
//        if($set->isActive())
//        {
//            dispatch(new UpdateInSearchia($set));
//        }
//        else{
//            dispatch(new DeleteFromSearchia($set));
//        }
        Cache::tags([
            'contentset_'.$set->id, 'set_search', 'set', 'activeContent', 'set_'.$set->id, 'set_'.$set->id.'_contents',
            'set_'.$set->id.'_activeContents'
        ])->flush();
    }

    /**
     * Handle the product "deleted" event.
     *
     * @param  Contentset  $set
     *
     * @return void
     */
    public function deleted(Contentset $set)
    {
        //
    }

    /**
     * Handle the product "restored" event.
     *
     * @param  Contentset  $set
     *
     * @return void
     */
    public function restored(Contentset $set)
    {
        //
    }

    /**
     * Handle the product "force deleted" event.
     *
     * @param  Contentset  $set
     *
     * @return void
     */
    public function forceDeleted(Contentset $set)
    {
        //
    }

    /**
     * When issuing a mass update via Eloquent,
     * the saved and updated model events will not be fired for the updated models.
     * This is because the models are never actually retrieved when issuing a mass update.
     *
     * @param  Contentset  $set
     */
    public function saving(Contentset $set)
    {


    }

    public function saved(Contentset $set)
    {
        $this->sendTagsOfTaggableToApi($set, $this->tagging);
        Cache::tags([
            'contentset_'.$set->id,
            'set_search',
            'set',
            'activeContent',
            'set_'.$set->id,
            'set_'.$set->id.'_contents',
            'set_'.$set->id.'_activeContents',
            'userAsset',
        ])->flush();
        $danaSets =
            DanaProductSetTransfer::where('contentset_id', $set->id)->where('dana_session_id', '!=', null)->get();
        if ($danaSets->isNotEmpty()) {
            foreach ($danaSets as $danaSet) {
                $danaCourseId =
                    DanaProductTransfer::where('product_id', $danaSet->product_id)->where('dana_course_id', '!=',
                        null)->first()?->dana_course_id;
                if (!is_null($danaCourseId)) {
                    UpdateDanaSessionName::dispatch($danaSet->dana_session_id, $set->small_name, $danaCourseId);
                }
            }
        }
    }
}
