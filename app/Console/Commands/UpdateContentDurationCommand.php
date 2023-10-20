<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Traits\Content\ContentControllerResponseTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateContentDurationCommand extends Command
{
    use ContentControllerResponseTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:updateContentDuration {--set=} {--content=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $setId = $this->option('set');
        $contentId = $this->option('content');
        if (isset($setId)) {
            $contents = Content::query()
                ->where('contentset_id', $setId)
                ->whereIn('contenttype_id', [config('constants.CONTENT_TYPE_VIDEO')])
                ->where('id', '>', 0)
                ->whereNull('redirectUrl')
                ->whereNull('duration')
                ->get();
        } elseif (isset($contentId)) {
            $contents = Content::query()->where('id', $contentId)
                ->whereIn('contenttype_id', [config('constants.CONTENT_TYPE_VIDEO')])
                ->where('id', '>', 0)
                ->whereNull('redirectUrl')
//                                        ->whereNull('duration')
                ->get();
        } else {
//            $contents = Content::whereIn('contenttype_id' , [config('constants.CONTENT_TYPE_VIDEO')])
//                ->where('id' , '>' , 0)
//                ->whereNull('redirectUrl')
//                ->whereNull('duration')
//                ->get();
            $contents = Content::whereIn('contenttype_id', [config('constants.CONTENT_TYPE_VIDEO')])
                ->whereIn('contentset_id', [
                    1617,
                    1618,
                    1619,
                    1620,
                    1622,
                    1623,
                    1624,
                    1625,
                    1626,
                    1627,
                    1628,
                    1629,
                    1630,
                    1631,
                    1649,
                    1732,
                    1761,
                ])
                ->where('id', '>', 0)
                ->whereNull('redirectUrl')
                ->whereNull('duration')
                ->get();

        }


        $count = $contents->count();
//        if (!$this->confirm("$count contents found, Do you want to continue?", true))
//        {
//            return false;
//        }

        $this->setContentCollectionDuration($contents);
        Artisan::call('cache:clear');
        return 0;
    }

    /**
     * @param $contents
     *
     * @return void
     */
    private function setContentCollectionDuration($contents): void
    {
        $bar = $this->output->createProgressBar($contents->count());
        $bar->start();
        /** @var Content $content */
        foreach ($contents as $content) {
            $this->setContentDuration($content);
            $bar->advance();
        }
        $bar->finish();
    }

    /**
     * @param  Content  $content
     *
     * @return void
     */
    private function setContentDuration(Content $content): void
    {
        $duration = $content->getContentDuration();
        /*if(is_null($duration))
        {
            $files  = $content->file_for_app;
            $links = '' ;
            if($content->contenttype_id == Content::CONTENT_TYPE_VIDEO)
            {
                $type = 'video' ;
                $videos    = $files?->get('video');
                if(!is_null($videos))
                {
                    foreach ($videos as $file)
                    {
                        $link = $file?->link;
                        if(!is_null($link))
                        {
                            $links .= ' , '.$link ;
                        }
                    }
                }
            }else
            {
                $type = 'pamphlet' ;
                $pamphlets    = $files?->get('pamphlet');
                $pamphlet = $pamphlets[0];
                if(!is_null($pamphlet))
                {
                    $link = $pamphlet?->link;
                    if(!is_null($link))
                    {
                        $links .= ' , '.$link ;
                    }
                }
            }

            if(strlen($links) > 0)
            {
                Log::channel('debug')->debug($type.' '.$content->id.' : '. $links);
            }

            continue;
        }*/
        if (!is_null($duration)) {
            $content->duration = $duration;
            $content->updateWithoutTimestamp();
        }
    }
}
