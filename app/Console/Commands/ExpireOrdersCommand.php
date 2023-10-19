<?php

namespace App\Console\Commands;

use App\Models\Orderproduct;
use Illuminate\Console\Command;

class ExpireOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:orders:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire all orders';

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
        $orderproducts = Orderproduct::whereHas('order', function ($query) {
            $query->paid();
        });

        if (!$this->confirm("{$orderproducts->count()} Order products found. Do you wish to continue?", true)) {
            $this->info('Done!');
            return;
        }

        $main = $this->output->createProgressBar($orderproducts->count());
        $orderproducts->update([
            'expire_at' => now()
        ]);
        $main->finish();

        $this->info("\n".'Done!');
        $this->newLine();
        return 0;
    }
}
