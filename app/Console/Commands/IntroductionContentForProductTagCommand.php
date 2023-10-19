<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Traits\APIRequestCommon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class IntroductionContentForProductTagCommand extends Command
{
    use APIRequestCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:seed:tag:introductionContentForProduct {product : The ID of the product}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'adds introducer video contents for a product';

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
        $productId = (int) $this->argument('product');
        if ($productId > 0) {
            try {
                $product = Product::findOrFail($productId);
            } catch (ModelNotFoundException $exception) {
                $this->error($exception->getMessage());

                return null;
            }
            if ($this->confirm('You have chosen\n\r '.$product->name.'. \n\rDo you wish to continue?', true)) {
                $this->performTaggingTaskForAProduct($product);
            }
        } else {
            $this->performTaggingTaskForAllProducts();
        }
    }

    private function performTaggingTaskForAProduct(Product $product)
    {
        $introducerContents = optional($product->sample_contents)->tags;
        $this->setRelatedContentsTags($product, isset($introducerContents) ? $introducerContents : [],
            Product::SAMPLE_CONTENTS_BUCKET);
    }

    private function setRelatedContentsTags(Product $product, array $contentIds, string $bucket): bool
    {
        $itemTagsArray = [];
        foreach ($contentIds as $id) {
            $itemTagsArray[] = 'Content-'.$id;
        }

        $params = [
            'tags' => json_encode($itemTagsArray, JSON_UNESCAPED_UNICODE),
        ];

        $response = $this->sendRequest(config('constants.TAG_API_URL')."id/$bucket/".$product->id, 'PUT', $params);
        return 1;
    }

    private function performTaggingTaskForAllProducts(): void
    {
        $products = Product::all();
        $productCount = $products->count();
        if (!$this->confirm("$productCount products found. Do you wish to continue?", true)) {
            $this->info('DONE!');
            return;
        }
        $bar = $this->output->createProgressBar($productCount);
        foreach ($products as $product) {
            $this->performTaggingTaskForAProduct($product);
            $bar->advance();
        }
        $bar->finish();

        $this->info('DONE!');
    }
}
