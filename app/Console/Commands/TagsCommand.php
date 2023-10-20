<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\Product;
use App\Traits\APIRequestCommon;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TagsCommand extends Command
{
    use APIRequestCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:tag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'inits tags';

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
        $bar = $this->output->createProgressBar(1);
        $this->allTaggable($bar);
        $bar->finish();
        $this->info('Done!');

    }

    private function allTaggable($bar)
    {
        $this->contentTag();
        $bar->advance();

        $this->contentSetTag();
        $bar->advance();

        $this->productTag();
        $bar->advance();
    }

    private function contentTag()
    {
        $bucket = 'content';
        $items = Content::orderBy('id')
            ->where('enable', 1)
//            ->where('id', '>', '2255')
            ->get();
        $this->setItemsTags($items, $bucket);
        $this->info('Content Tag Done!');
    }

    private function setItemsTags($items, $bucket)
    {
        $counter = 0;
        $bar = $this->output->createProgressBar($items->count());
        foreach ($items as $item) {
            if (!isset($item)) {
//                $this->error("invalid item at bucket: " . $bucket . " counter" . $counter);
                Log::channel('debug')->error('invalid item at bucket: '.$bucket.' counter'.$counter);
                $counter++;
                continue;
            }
            if (!isset($item->tags)) {
//                $this->error("no tags found for bucket: " . $bucket . " item" . $item->id);
                Log::channel('debug')->error('no tags found for bucket: '.$bucket.' item'.$item->id);
                $counter++;
                continue;
            }
            [
                $item,
                $counter,
            ] = $this->setAnItemTags($bucket, $item, $counter);
            $bar->advance();
        }
        $bar->finish();
        $this->info($bucket.' Done!');
    }

    /**
     * @param $bucket
     * @param $item
     * @param $counter
     *
     * @return array
     */
    private function setAnItemTags($bucket, $item, $counter): array
    {

        $itemTagsArray = $item->tags->tags;
        if (is_array($itemTagsArray) && !empty($itemTagsArray) && isset($item['id'])) {
            $params = [
                'tags' => json_encode($itemTagsArray),
            ];
            if (isset($item->created_at) && strlen($item->created_at) > 0) {
                $params['score'] = Carbon::createFromFormat('Y-m-d H:i:s', $item->created_at)->timestamp;
            }
            try {
                $response = $this->sendRequest(
                    config('constants.TAG_API_URL')."id/$bucket/".$item->id,
                    'PUT',
                    $params
                );
                if ($response['statusCode'] != Response::HTTP_OK) {
                    $this->error('item #'.$item['id'].' failed. response : '.$response['statusCode']);
                }
            } catch (Exception    $e) {
//                $this->error("item #" . $item["id"] . " failed.");
                Log::channel('debug')->error('item #'.$item['id'].' failed.');
            }
            $counter++;

        } else {
            if (is_array($itemTagsArray) && empty($itemTagsArray)) {
                $counter++;
//                $this->error("no tags found for bucket: " . $bucket . " item" . $item->id);
                Log::channel('debug')->error('no tags found for bucket: '.$bucket.' item'.$item->id);
            }
        }
        return [
            $item,
            $counter,
        ];
    }

    private function contentSetTag()
    {
        $bucket = 'contentset';
        $items = Contentset::orderBy('id')
            ->where('enable', 1)
            ->get();
        $this->setItemsTags($items, $bucket);
        $this->info('contentSet Tag Done!');
    }

    private function productTag()
    {
        $bucket = 'product';
        $items = Product::orderBy('id')
            ->where('enable', 1)
            ->where('seller', 1)
            ->get();
        $this->setItemsTags($items, $bucket);
        $this->info('Product Tag Done!');
    }
}
