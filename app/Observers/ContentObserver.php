<?php

namespace App\Observers;

use App\Classes\Search\Tag\TaggingInterface;
use App\Jobs\DanaTransferJob;
use App\Jobs\DeleteOrTransferContentToDanaProductJob;
use App\Jobs\InsertContentToSatra;
use App\Models\Content;
use App\Models\DanaProductTransfer;
use App\Models\Product;
use App\Traits\TaggableTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Request;

class ContentObserver
{
    private $tagging;

    use TaggableTrait;

    public function __construct(TaggingInterface $tagging)
    {
        $this->tagging = $tagging;
    }

    /**
     * Handle the content "created" event.
     *
     * @param  Content  $content
     *
     * @return void
     */
    public function created(Content $content)
    {
        dispatch(new InsertContentToSatra($content));
    }

    /**
     * Handle the content "updated" event.
     *
     * @param  Content  $content
     *
     * @return void
     */

    public function updating(Content $content)
    {
        Log::channel('updatedAtForContent')->debug('UPDATING', [
            'setId' => $content->getOriginal('contentset_id'),
            'updated_at' => $content->updated_at,
            'uri' => Request::getRequestUri(),
            'method' => Request::method(),
            'user_id' => optional(Request::user())->id,
        ]);
    }

    public function updated(Content $content)
    {

        Log::channel('updatedAtForContent')->debug('UPDATED', [
            'setId' => $content->contentset_id,
            'updated_at' => $content->updated_at,
            'uri' => Request::getRequestUri()
        ]);

//        (new SatraSyncer())->updateContentInfo($content);
//        if($content->isActive())
//        {
//            dispatch(new UpdateInSearchia($content));
//        }
//        else{
//            dispatch(new DeleteFromSearchia($content));
//        }
        $cacheTagsForFlush = [
            'content_'.$content->id,
            'set_'.$content->contentset_id.'_contents',
            'set_'.$content->contentset_id.'_setMates',
            'content_search',
            'set_search',
            'activeContent',
            'set_'.$content->contentset_id, 'set_'.$content->contentset_id.'_contents',
            'set_'.$content->contentset_id.'_activeContents',
        ];
        Cache::tags($cacheTagsForFlush)->flush();
        Cache::tags($cacheTagsForFlush)->flush();
    }

    /**
     * Handle the content "deleted" event.
     *
     * @param  Content  $content
     *
     * @return void
     */
    public function deleted(Content $content)
    {
        $cacheTagsForFlush = [];
        foreach ($content->plans as $plan) {
            $cacheTagsForFlush[] = 'event_abrisham1401_whereIsKarvan_'.optional($plan->studyplan)->plan_date;
        }
        Cache::tags($cacheTagsForFlush)->flush();
    }

    /**
     * Handle the content "restored" event.
     *
     * @param  Content  $content
     *
     * @return void
     */
    public function restored(Content $content)
    {
        //
    }

    /**
     * Handle the content "force deleted" event.
     *
     * @param  Content  $content
     *
     * @return void
     */
    public function forceDeleted(Content $content)
    {
        //
    }

    /**
     * When issuing a mass update via Eloquent,
     * the saved and updated model events will not be fired for the updated models.
     * This is because the models are never actually retrieved when issuing a mass update.
     *
     * @param  Content  $content
     */
    public function saving(Content $content)
    {
        $content->template_id = $this->findTemplateIdOfaContent($content);
    }

    /**
     * @param $content
     *
     * @return int|null
     */
    private function findTemplateIdOfaContent($content)
    {
        return [
            Content::CONTENT_TYPE_PAMPHLET => Content::CONTENT_TEMPLATE_PAMPHLET,
            Content::CONTENT_TYPE_EXAM => Content::CONTENT_TEMPLATE_EXAM,
            Content::CONTENT_TYPE_VIDEO => Content::CONTENT_TEMPLATE_VIDEO,
            Content::CONTENT_TYPE_ARTICLE => Content::CONTENT_TEMPLATE_ARTICLE,
        ][$content->contenttype_id] ?? null;
    }

    public function saved(Content $content)
    {
        if (isset($content->redirectUrl)) {
//            if ($content->isFree && auth()->check() && auth()->user()->isAbleTo(config('constants.REMOVE_EDUCATIONAL_CONTENT_FILE_ACCESS'))) {
//                event(new ContentRedirected($content));
//            }
            $this->removeTagsOfTaggable($content, $this->tagging);
        } else {
            $this->sendTagsOfTaggableToApi($content, $this->tagging);
        }

        $cacheTagsForFlush = [
            'content_'.$content->id,
            'set_'.$content->contentset_id.'_contents',
            'set_'.$content->contentset_id.'_setMates',
            'content_search',
            'set_search',
            'activeContent',
            'set_'.$content->contentset_id, 'set_'.$content->contentset_id.'_contents',
            'set_'.$content->contentset_id.'_activeContents',
            'userAsset',
        ];
        foreach ($content->plans as $plan) {
            $cacheTagsForFlush[] = 'event_abrisham1401_whereIsKarvan_'.optional($plan->studyplan)->plan_date;
        }
        Cache::tags($cacheTagsForFlush)->flush();

        $proudctIds = $content->set?->products()->get()->pluck('id');
        $foriatIds = array_merge(Product::ALL_FORIYAT_110_PRODUCTS, [Product::ARASH_TETA_SHIMI, Product::TETA_ADABIAT]);
        $productIntersect = $proudctIds->intersect($foriatIds)->all();
        if (DanaProductTransfer::whereIn('product_id', $proudctIds->toArray())->where('insert_type', 2)->exists()) {
            DeleteOrTransferContentToDanaProductJob::dispatch($content);
        } else {
            if (!empty($productIntersect)) {
                DanaTransferJob::dispatch($content);
            }
        }
    }
}
