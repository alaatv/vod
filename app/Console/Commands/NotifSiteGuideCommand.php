<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Notifications\SiteGuideCorrection;
use Illuminate\Console\Command;

class NotifSiteGuideCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:notif:siteGuide';

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
        $orders = Order::whereHas('ordermanagercomments', function ($q2) {
            $q2->where('comment', 'kmt');
        })->get();
        $count = $orders->count();
        if (!$this->confirm("$count orders found. Do you wish to continue?", true)) {

            $this->info('Done!');
            return;
        }
        $bar = $this->output->createProgressBar($count);
        foreach ($orders as $order) {
            $user = $order->user;
            if (!isset($user)) {
                $this->warn('Order does not have user:'.$order->id);
                $bar->advance();
                continue;
            }

            if ($order->orderproducts->where('product_id', Product::ARASH_PACK_RIYAZI)->isNotEmpty()) {
                $bar->advance();
                continue;
                $productName = 'پک آرش ریاضی کنکور';
            } elseif ($order->orderproducts->where('product_id', Product::ARASH_PACK_TAJROBI)->isNotEmpty()) {
                $productName = 'پک آرش تجربی کنکور';
            } elseif ($order->orderproducts->where('product_id', Product::ARASH_PACK_ENSANI)->isNotEmpty()) {
                $productName = 'پک آرش انسانی کنکور';
            }

            if (!isset($productName)) {
                $this->warn('User does not have Arash pack:'.$user->id);
                $bar->advance();
                continue;
            }

//                $user->notify(new SiteGuide($productName, route('web.user.asset'), 'https://AlaaTV.com/t', 202, $user->nationalCode));
            $user->notify(new SiteGuideCorrection($productName, 'پک آرش ریاضی کنکور'));
            $bar->advance();
        }
        $bar->finish();


        $this->info('Done!');
    }
}
