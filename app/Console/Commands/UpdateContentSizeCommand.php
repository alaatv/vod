<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Traits\CharacterCommon;
use App\Traits\Content\ContentControllerResponseTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class UpdateContentSizeCommand extends Command
{
    use ContentControllerResponseTrait;
    use CharacterCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:updateContentSize';

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
        $contents =
            Content::whereIn('contenttype_id',
                [config('constants.CONTENT_TYPE_PAMPHLET'), config('constants.CONTENT_TYPE_VIDEO')])
                ->where('id', '>', 0)
//                            ->where('file' , 'like' , '%"size":null%')
                ->whereNull('redirectUrl')
                ->where('enable', 1)
                ->whereNull('deleted_at')
                ->get();

        $count = $contents->count();
        $bar = $this->output->createProgressBar($count);
        if (!$this->confirm("$count contents found, Do you want to continue?", true)) {

            return 0;
        }

        /** @var Content $content */
        foreach ($contents as $content) {
            $duration = $content->getContentDuration();
            $files = $this->setContentFileSize($content);

            if (isset($files) && !empty($files) && $content->urlExist()) {
                $content->file = collect($files);
                $content->duration = $duration;
            } else {
                $content->enable = 0;
            }
            $content->updateWithoutTimestamp();

            $bar->advance();
        }

        Artisan::call('cache:clear');

        $bar->finish();


        return 0;
    }
}
