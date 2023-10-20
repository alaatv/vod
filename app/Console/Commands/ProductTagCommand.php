<?php

namespace App\Console\Commands;

use App\Classes\Search\Tag\TaggingInterface;
use App\Models\Product;
use App\Traits\TaggableTrait;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductTagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:seed:tag:product {product : The ID of the product}';

    use TaggableTrait;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'adds tags for a product';

    private $tagging;

    /**
     * ProductTagCommand constructor.
     *
     * @param  TaggingInterface  $tagging
     */
    public function __construct(TaggingInterface $tagging)
    {
        parent::__construct();
        $this->tagging = $tagging;
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
        $this->sendTagsOfTaggableToApi($product, $this->tagging);
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
