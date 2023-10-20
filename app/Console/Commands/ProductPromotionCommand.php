<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ProductPromotionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:productPromotion {discount}';

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
        $products = Product::whereNotIn('id',
            [Product::DONATE_PRODUCT_5_HEZAR, Product::CUSTOM_DONATE_PRODUCT])->active()->get();

        $discount = $this->argument('discount');

        $count = $products->count();
        if (!$this->confirm("$count active products found, Do you want to continue?", true)) {

            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        foreach ($products as $product) {
            $product->update(['discount' => $discount]);
            $bar->advance();
        }

        $bar->finish();


        return 0;
    }
}
