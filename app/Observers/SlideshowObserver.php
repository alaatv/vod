<?php

namespace App\Observers;

use App\Models\Slideshow;
use App\Repositories\SlideshowRepo;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

class SlideshowObserver
{
    /**
     * Handle the slide show "created" event.
     *
     * @param  Slideshow  $slidshow
     *
     * @return void
     */
    public function created(Slideshow $slidshow)
    {
        //
    }

    /**
     * Handle the slide show "updated" event.
     *
     * @param  Slideshow  $slidshow
     *
     * @return void
     */
    public function updated(Slideshow $slidshow)
    {
        //
    }

    /**
     * Handle the slide show "deleted" event.
     *
     * @param  Slideshow  $slidshow
     *
     * @return void
     */
    public function deleted(Slideshow $slidshow)
    {
        //
    }

    /**
     * Handle the slide show "restored" event.
     *
     * @param  Slideshow  $slidshow
     *
     * @return void
     */
    public function restored(Slideshow $slidshow)
    {
        //
    }

    /**
     * Handle the slide show "force deleted" event.
     *
     * @param  Slideshow  $slidshow
     *
     * @return void
     */
    public function forceDeleted(Slideshow $slidshow)
    {
        //
    }


    public function saving(Slideshow $slideshow)
    {
        $file = $slideshow->photo;
        if (!$file instanceof UploadedFile) {
            return;
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = basename($file->getClientOriginalName(), '.'.$extension).'_'.date('YmdHis').'.'.$extension;

        return $slideshow->setPhoto($file, config('disks.homeSlideShowPicMinio'), $fileName) ?
            null :
            myAbort(Response::HTTP_BAD_REQUEST, 'خطایی در هنگام آپلود تصویر به وجود آمده است!');
    }

    public function saved(Slideshow $slideshow)
    {
        $slideBlockTypes = ['slideshow_'.$slideshow->id];

        foreach ($slideshow->blocks as $block) {
            $slideBlockTypes[] = $block->blockType->name;
        }

        $slideshowBlocks = SlideshowRepo::getSlideshowBlocks($slideshow)->pluck('id')->toArray();
        $requestBlocks = array_map('intval', request('block_type_ids') ?? []);
        $totalBlocks = array_unique(array_merge($slideshowBlocks, $requestBlocks));

        if (!empty($totalBlocks)) {
            foreach ($totalBlocks as $blockId) {
                Cache::tags('banner'.$blockId)->flush();
            }
        }

        Cache::tags($slideBlockTypes)->flush();
    }
}
