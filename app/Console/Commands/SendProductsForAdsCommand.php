<?php

namespace App\Console\Commands;

use App\Classes\Marketing\Yektanet\Yektanet;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class SendProductsForAdsCommand extends Command
{
    public const HEADERS = [
        'Authorization' => 'Token 187ce5f3da02185b7c8351b159d35d370ef26c5',
        'Content-Type' => 'application/json',
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:ads:send:products';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sending all products info to Yektanet for marketing';
    private Yektanet $yektanet;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Yektanet $yektanet)
    {
        $this->yektanet = $yektanet;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::all();

        $count = $products->count();

        if (!$this->confirm("$count products found , Do you wish to continue?")) {
            $this->info('Aborted');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $this->line("\n");
        $bar->start();

        foreach ($products as $product) {
            $result = $this->yektanet->sendSingleProduct($product);
            if (Arr::get($result, 'statusCode') != Response::HTTP_OK) {
                $this->line("\n");
                $this->info('Product '.$product->id.' was not sent successfully!');
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info('Done');
    }
}
