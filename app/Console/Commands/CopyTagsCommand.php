<?php

namespace App\Console\Commands;

use App\Models\Contentset;
use App\Traits\APIRequestCommon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Response;

class CopyTagsCommand extends Command
{
    use APIRequestCommon;

    protected $signature = 'alaaTv:copyTags {setIds}';

    protected $description = 'Copy tags of content to redis';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $argument = $this->argument('setIds');

        $setIds = explode(',', $argument);

        $sets = Contentset::whereIn('id', $setIds)->get();
        $setsCount = $sets->count();

        if (!$this->confirm("$setsCount sets found , Do you wish to continue?")) {
            return 0;
        }

        $outerBar = $this->output->createProgressBar($setsCount);
        foreach ($sets as $set) {
            $this->info("\n");
            $this->info("Copy set #$set->id contents:");

            $contents = $set->contents->sortBy('order');

            if ($contents->isEmpty()) {
                $this->info("No contents found for set #{$set->id}");
                $outerBar->advance();
                continue;
            }

            $firstContent = $contents[0];

            $itemTagsArray = optional($firstContent->tags)->tags;
            if (!isset($itemTagsArray) || empty($itemTagsArray)) {

                $firstContent = $contents[1];
                $itemTagsArray = optional($firstContent->tags)->tags;
                if (!isset($itemTagsArray) || empty($itemTagsArray)) {
                    $this->info("No tags found for the first content of set #{$set->id}");
                    $outerBar->advance();
                    continue;
                }
            }

            $innerBar = $this->output->createProgressBar(count($contents));
            foreach ($contents as $content) {
                $innerBar->advance();

                $content->tags = $itemTagsArray;
                $content->update();

                $content->fresh();

                $params = [
                    'tags' => json_encode($itemTagsArray, JSON_UNESCAPED_UNICODE),
                ];

                if (isset($content->created_at) && strlen($content->created_at) > 0) {
                    $params['score'] = Carbon::createFromFormat('Y-m-d H:i:s', $content->created_at)->timestamp;
                }

                $response = $this->sendRequest(config('constants.TAG_API_URL').'id/content/'.$content->id, 'PUT',
                    $params);
                if ($response['statusCode'] != Response::HTTP_OK) {
                    $this->comment("Error on tagging content #$content->id");
                }
            }
            $innerBar->finish();
            $outerBar->advance();
        }
        $outerBar->finish();

        $this->info("\n");
        $this->info('Done!');
        return 0;
    }
}
