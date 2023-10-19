<?php

namespace App\Console\Commands;

use App\Models\Contentset;

use App\Models\Product;
use App\Traits\APIRequestCommon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class RecommenderContentForProductTagCommand extends Command
{
    use APIRequestCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:seed:tag:recommenderContentForProduct {product : The ID of the product}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'adds recommender contents for a product';

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

                return 0;
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
        $recommenderItems = optional($product->recommender_contents)->recommenders;
        $recommenderContents = optional($recommenderItems)->contents;
        $recommenderSets = optional($recommenderItems)->sets;
        $this->setRecommenderContentsTags($product, !is_null($recommenderContents) ? $recommenderContents : [],
            !is_null($recommenderSets) ? $recommenderSets : [], Product::RECOMMENDER_CONTENTS_BUCKET);
    }

    private function setRecommenderContentsTags(
        Product $product,
        array $contentIds,
        array $setIds,
        string $bucket
    ): bool {
        $itemTagsArray = [];
        $itemTagsArray = array_merge($itemTagsArray, $contentIds);

        foreach ($setIds as $id) {
            $set = Contentset::Find($id);
            if (!isset($set)) {
                continue;
            }

            $itemTagsArray = array_merge($itemTagsArray, $set->contents->pluck('id')->toArray());
        }

        $params = [
            'tags' => json_encode($itemTagsArray, JSON_UNESCAPED_UNICODE),
        ];

        $response = $this->sendRequest(config('constants.TAG_API_URL')."id/$bucket/".$product->id, 'PUT', $params);
        return true;
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
