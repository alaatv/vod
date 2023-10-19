<?php

namespace App\Console\Commands;


use App\Jobs\GiveGiftProductsJob;
use App\Traits\GiveGift\GiveGift;
use App\Traits\GiveGift\GiveGiftPlans;
use App\Traits\GiveGift\GiveGiftQueries;
use App\Traits\GiveGift\GiveGiftsHelper;
use Illuminate\Console\Command;

class GiveGiftProductsCommand extends Command implements GiveGift
{
    use GiveGiftPlans;
    use GiveGiftQueries;
    use GiveGiftsHelper;

    protected $signature = 'alaaTv:GiveGifts {action : Enter plan among plans that sets in GiveGift interface}';

    protected $description = 'give gift to user base selected plan';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $action = $this->argument('action');

        // check handler of this method be in GiveGiftPlans trait
        // for each plane we have a method with the same name in GiveGiftPlans trait
        if (!$this->actionIsValid($action)) {
            $this->comment("\nPlan {$action} Not Defined\n");
            return 0;
        }

        $orders = self::selectOrders($action);
        $count = $orders->count();
        if (!$this->confirm("$count orders found, Do you wish to continue?", true)) {
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        foreach ($orders as $order) {
            dispatch(new GiveGiftProductsJob($order, $action));
            $bar->advance();
        }
        $bar->finish();
        $this->info('Done!');
        return 0;
    }
}
