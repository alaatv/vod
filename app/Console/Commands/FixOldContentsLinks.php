<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Contentset;
use Illuminate\Console\Command;

class FixOldContentsLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:fix:oldContents:links';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes old contents links';

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
        $contentsets = Contentset::where('id', '<=', 87)->get();
        $contentsetCount = $contentsets->count();
        if (!$this->confirm("$contentsetCount sets found , Do you want to continue ?", true)) {
            return 0;
        }
        $contentSetBar = $this->output->createProgressBar($contentsetCount);
        foreach ($contentsets as $contentset) {
            $contents = $contentset->contents;
            $this->info('Processing '.$contents->count().' contents...');
            $this->info("\n\n");
            /** @var Content $content */
            foreach ($contents as $content) {
                if ($content->contenttype_id != config('constants.CONTENT_TYPE_VIDEO')) {
                    continue;
                }

                $videos = json_decode($content->getRawOriginal('file'));
                foreach ($videos as $video) {
                    if (!isset($video->url)) {
                        $this->info('content '.$content->id.' doesnt have video url.');
                        $this->info("\n");
                        continue;
                    }

                    if (!isset($video->res)) {
                        $this->info('content '.$content->id.' doesnt have video res.');
                        $this->info("\n");
                        continue;
                    }

                    $link = $video->url;
                    $res = $video->res;
                    $contentsetId = $content->contentset_id;
                    if (strpos($link, '/media/') === false) {
                        continue;
                    }
                    if ($res == '720p' && strpos($link, '/media/HD_720p/') !== false) {
                        $splitted1 = explode('/media/', $link);
                        $splitted = explode('/HD_720p/', $link);
                        $subPath = '/media/'.$contentsetId.'/HD_720p/'.basename($splitted[1]);
                        $video->url = $splitted1[0].$subPath;
                        $video->fileName = $subPath;
                    } else {
                        if ($res == '480p' && strpos($link, '/media/hq/') !== false) {
                            $splitted1 = explode('/media/', $link);
                            $splitted = explode('/hq/', $link);
                            $subPath = '/media/'.$contentsetId.'/hq/'.basename($splitted[1]);
                            $video->url = $splitted1[0].$subPath;
                            $video->fileName = $subPath;
                        } else {
                            if ($res == '240p' && strpos($link, '/media/240p/') !== false) {
                                $splitted1 = explode('/media/', $link);
                                $splitted = explode('/240p/', $link);
                                $subPath = '/media/'.$contentsetId.'/240p/'.basename($splitted[1]);
                                $video->url = $splitted1[0].$subPath;
                                $video->fileName = $subPath;
                            }
                        }
                    }

                }
                $content->setRawAttributes(['file' => json_encode($videos)]);
                if ($content->update()) {
                    continue;
                }
                $this->info('content '.$content->id.' could not be updated.');
                $this->info("\n");
            }
            $contentSetBar->advance();
            $this->info("\n");
        }
        $contentSetBar->finish();

        $this->info('Command Finished!');
        $this->info("\n\n");
    }
}
