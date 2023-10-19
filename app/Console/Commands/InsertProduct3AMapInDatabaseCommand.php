<?php

namespace App\Console\Commands;

use App\Models\_3aExam;

use App\Models\Product;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class InsertProduct3AMapInDatabaseCommand extends Command
{

    public const EXAM_ID_MAP = [
        590 => '610e88c167a9312bc560e722',
        495 => '60924621ade9711661048075',
        492 => '609245f84e740368c56b1ae9',
        489 => '6092461b9b01b716bc2c153a',
        591 => '6135be32e0db6947171ef9a0',
        592 => '6135be29e0db6947171ef99d',
        593 => '6135be1ce0db6947171ef998',
        595 => '6135bddbe0db6947171ef98a',
        594 => '6134aa6d567a60794c3c3d17',
        596 => '6134a58b567a60794c3c3cdd',
        597 => '614c6e610b9442588a76d626',
        598 => '614c6e830b9442588a76d62a',
        599 => '614c6e8a0b9442588a76d62c',
        602 => '617f75015acc124a286ca9d3',
        603 => '617f74ba5acc124a286ca9cc',
        601 => '617f75795acc124a286ca9e1',
        605 => '617f75525acc124a286ca9d8',
        606 => '617f743e5acc124a286ca9c0',
        604 => '617f759b5acc124a286ca9e3',
        631 => '61c9826159f18714ed75fc43',
        630 => '61ba25dcc5a4aa3a840aa4bb',
        632 => '61b9f1333ddbce63d60e3f61',
        641 => '61c0da66be7e9e0ecb3a6b63',
        637 => '61c0dab1be7e9e0ecb3a6b65',
        639 => '61c984a7fe1bf8699a378d13',
        634 => '61ca3ca3769acf046760f023',
        640 => '61ca3d25769acf046760f025',
        636 => '61e50d7747d3a13adc0854de',
        633 => '61e5145416e9c640907c3838',
        644 => '61e5147d16e9c640907c3846',
        635 => '61e5e5b9a1a3f6600a66a87b',
        645 => '61e69a31540bc30dde3c2a09',
        638 => '61e69a5d540bc30dde3c2a16',
        646 => '61e69a81540bc30dde3c2a1a',
    ];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:fill:3a_exams_table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create 3a exams and attach them to related product';

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
     * @return int
     */
    public function handle()
    {
        $productsExamsMap = self::EXAM_ID_MAP;

        if (!$this->confirm(count($productsExamsMap).' Products found, Do you want to continue?')) {
            return 0;
        }

        $bar = $this->output->createProgressBar(count($productsExamsMap));
        $bar->start();

        foreach ($productsExamsMap as $productId => $examId) {

            $product = $this->tryFindProduct($productId);
            if (!$product) {
                continue;
            }

            $this->tryCreateExam($examId, $product);

            $bar->advance();

        }

        return 0;
    }

    protected function tryFindProduct(int $productId): ?Product
    {
        try {

            return Product::find($productId);

        } catch (Exception $exception) {
            Log::channel('debug')->info("product {$productId} not found");
        }

        return null;
    }

    protected function tryCreateExam(string $examId, Product $product): ?_3aExam
    {
        try {
            $product->exams()->create(['id' => $examId, 'title' => $product->name]);
        } catch (Exception $exception) {
            Log::channel('debug')->info("error in exam {$examId} creation");
        }

        return null;
    }
}
