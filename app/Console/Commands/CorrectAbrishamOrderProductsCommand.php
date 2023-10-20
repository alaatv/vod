<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderproductRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CorrectAbrishamOrderProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:correct:Abrisham:orderproducts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Correct Abrisham orderproducts that don\'t insert';

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
        $orders = Order::with('orderproducts')->paidAndClosed()->whereHas('orderproducts', function ($query) {
            $query->whereIn('product_id', [
                Product::RAHE_ABRISHAM99_PACK_TAJROBI, Product::RAHE_ABRISHAM99_PACK_RIYAZI,
                Product::RAHE_ABRISHAM1401_PACK_OMOOMI
            ]);
        })->where('completed_at', '>=', '2021-07-23 00:00:00')->get();

        foreach ($orders as $order) {
            $this->giveAbrishamOrderProducts($order);
            $this->deleteInvalidOrderProducts($order);
        }
        $this->info('orderproducts successfully applied!');

        return 0;
    }

    public function giveAbrishamOrderProducts(Order $order): void
    {

        if ($this->hasRaheAbrishamPack($order, Product::RAHE_ABRISHAM99_PACK_RIYAZI)) {
            $this->giveAbrishamRiyaziProducts($order);
        }

        if ($this->hasRaheAbrishamPack($order, Product::RAHE_ABRISHAM99_PACK_TAJROBI)) {
            $this->giveAbrishamTajrobiProducts($order);
        }

        if ($this->hasRaheAbrishamPack($order, Product::RAHE_ABRISHAM1401_PACK_OMOOMI)) {
            $this->giveAbrishamOmoomiProducts($order);
        }
    }

    public function hasRaheAbrishamPack(Order $order, int $pack_id): bool
    {
        return $order->orderproducts->where('product_id', $pack_id)->where('cost', '>', 0)->isNotEmpty();
    }

    public function giveAbrishamRiyaziProducts(Order $order): void
    {
        $riyazi = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI)->first();
        $shimi = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM99_SHIMI)->first();

        if (!$riyazi) {
            $riaziPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI)->first()->price,
                'base', 0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI,
                $riaziPrice);
        }

        if (!$shimi) {
            $shimiPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM99_SHIMI)->first()->price, 'base', 0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM99_SHIMI, $shimiPrice);
        }
    }

    public function giveAbrishamTajrobiProducts(Order $order): void
    {
        $zist = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM99_ZIST)->first();
        $shimi = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM99_SHIMI)->first();
        $RT = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI)->first();

        if (!$zist) {
            $riaziPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM99_ZIST)->first()->price, 'base', 0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM99_ZIST, $riaziPrice);
        }

        if (!$shimi) {
            $shimiPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM99_SHIMI)->first()->price, 'base', 0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM99_SHIMI, $shimiPrice);
        }

        if (!$RT) {
            $RTPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI)->first()->price, 'base',
                0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI, $RTPrice);
        }
    }

    public function giveAbrishamOmoomiProducts(Order $order): void
    {
        $zaban = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM1401_ZABAN)->first();
        $dini = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM1401_DINI)->first();
        $arabi = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM1401_ARABI)->first();
        $adabiyat = $order->orderproducts()->where('product_id', Product::RAHE_ABRISHAM1401_ADABIYAT)->first();

        if (!$zaban) {
            $zabanPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM1401_ZABAN)->first()->price, 'base', 0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM1401_ZABAN, $zabanPrice);
        }

        if (!$dini) {
            $diniPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM1401_DINI)->first()->price, 'base', 0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM1401_DINI, $diniPrice);
        }

        if (!$arabi) {
            $arabiPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM1401_ARABI)->first()->price, 'base', 0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM1401_ARABI, $arabiPrice);
        }

        if (!$adabiyat) {
            $adabiyatPrice = Arr::get(Product::where('id', Product::RAHE_ABRISHAM1401_ADABIYAT)->first()->price, 'base',
                0);
            OrderproductRepo::createBasicOrderproduct($order->id, Product::RAHE_ABRISHAM1401_ADABIYAT, $adabiyatPrice);
        }
    }

    public function deleteInvalidOrderProducts(Order $order): void
    {
        if ($this->hasInvalidRaheAbrishamPack($order, Product::RAHE_ABRISHAM99_PACK_RIYAZI)) {
            $this->deleteAbrishamRiyaziProducts($order);
        }

        if ($this->hasInvalidRaheAbrishamPack($order, Product::RAHE_ABRISHAM99_PACK_TAJROBI)) {
            $this->deleteAbrishamTajrobiProducts($order);
        }

        if ($this->hasInvalidRaheAbrishamPack($order, Product::RAHE_ABRISHAM1401_PACK_OMOOMI)) {
            $this->deleteAbrishamOmoomiProducts($order);
        }

        $order->orderproducts()->whereIn('product_id', [
            Product::RAHE_ABRISHAM99_PACK_RIYAZI,
            Product::RAHE_ABRISHAM99_PACK_TAJROBI,
            Product::RAHE_ABRISHAM1401_PACK_OMOOMI
        ])->where('cost', 0)->delete();
    }

    public function hasInvalidRaheAbrishamPack(Order $order, int $pack_id): bool
    {
        return $order->orderproducts->where('product_id', $pack_id)->where('cost', 0)->isNotEmpty();
    }

    public function deleteAbrishamRiyaziProducts(Order $order): void
    {
        $order->orderproducts()->whereIn('product_id', [
            Product::RAHE_ABRISHAM99_PACK_RIYAZI, Product::RAHE_ABRISHAM99_SHIMI,
            Product::RAHE_ABRISHAM99_RIYAZIAT_RIYAZI
        ])->delete();
    }

    public function deleteAbrishamTajrobiProducts(Order $order): void
    {
        $order->orderproducts()->whereIn('product_id', [
            Product::RAHE_ABRISHAM99_PACK_TAJROBI, Product::RAHE_ABRISHAM99_ZIST, Product::RAHE_ABRISHAM99_SHIMI,
            Product::RAHE_ABRISHAM99_RIYAZIAT_TAJROBI
        ])->delete();
    }

    public function deleteAbrishamOmoomiProducts(Order $order): void
    {
        $order->orderproducts()->whereIn('product_id', [
            Product::RAHE_ABRISHAM1401_PACK_OMOOMI, Product::RAHE_ABRISHAM1401_ZABAN, Product::RAHE_ABRISHAM1401_DINI,
            Product::RAHE_ABRISHAM1401_ARABI, Product::RAHE_ABRISHAM1401_ADABIYAT
        ])->delete();
    }
}
