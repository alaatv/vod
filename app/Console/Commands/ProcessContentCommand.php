<?php

namespace App\Console\Commands;

use App\Models\Contentset;
use App\Traits\CharacterCommon;
use App\Traits\Content\ContentControllerResponseTrait;
use Illuminate\Console\Command;

class ProcessContentCommand extends Command
{
    use ContentControllerResponseTrait;
    use CharacterCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:content:process';

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
        $sets = Contentset::query()->whereIn('id', [
            780, 520, 385, 393, 397, 377, 381, 445, 301, 365, 369, 405, 413, 401, 357, 361, 461, 465, 389, 457, 313,
            345, 349, 353, 373, 317, 229, 321, 325, 329, 469, 441
        ])->get();

        $count = $sets->count();
        $bar = $this->output->createProgressBar($count);
        if (!$this->confirm("$count sets found. Do you wish to continue?", true)) {

            $this->info('Done');

            return 0;
        }
        foreach ($sets as $set) {
//                $set->update(['display' => 1]);
            $contents = $set->contents;
            $this->info($contents->count().' contents found');
            foreach ($contents as $content) {
                $content->update([
                    'isFree' => 1,
                    'display' => 1,
                ]);
            }
            $bar->advance();
        }

        $bar->finish();


        $this->info('Done');

        return 0;
    }

//    public function handle()
//    {
//
//        $contents = Content::query()->where('contentset_id' , 293)->where('contenttype_id' , config('constants.CONTENT_TYPE_VIDEO'))->get();
//
//        $count = $contents->count();
//        $bar = $this->output->createProgressBar($count);
//        if ($this->confirm("$count contents found. Do you wish to continue?", true)) {
//            foreach ($contents as $content)
//            {
//                $files = json_decode($content->getRawOriginal('file'));
//                $firstFile = Arr::get($files, 0);
//                $fileName = basename(optional($firstFile)->fileName) ;
//                if( $this->strIsEmpty($fileName) )
//                {
//                    $this->warn('Empty file name for content: '.$content->id);
//                    continue;
//                }
//
//                $files  = $this->makeContentFilesArray($content->contenttype_id, $content->contentset_id, $fileName, $content->isFree , ['480p' =>'1']);
//
//                if (!empty($files))
//                {
//                    $content->file = $this->makeFilesCollection($files);
//                    $content->updateWithoutTimestamp();
//                }
//
//
//                $bar->advance();
//            }
//
//            $bar->finish();
//        }
//
//        $this->info('Done');
//    }
}
