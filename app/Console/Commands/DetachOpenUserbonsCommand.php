<?php

namespace App\Console\Commands;

use App\Models\Userbon;
use Illuminate\Console\Command;

class DetachOpenUserbonsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:detach:open:userbons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detaches user bons from open orders';

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
        $openUserbons = Userbon::whereHas('orderproducts', function ($q) {
            $q->whereHas('order', function ($q2) {
                $q2->where('orderstatus_id', config('constants.ORDER_STATUS_OPEN'));
            });
        })->get();

        $openUserbonsCount = $openUserbons->count();

        if (!$this->confirm("There are $openUserbonsCount userbons, Would you like to continue?", true)) {
            return 0;
        }
        $this->info('Detaching userbons ...');
        $this->info("\n\n");

        $bar2 = $this->output->createProgressBar($openUserbonsCount);
        foreach ($openUserbons as $openUserbon) {
            /** @var Userbon $usedUserbon */
            $openUserbon->update([
                'usedNumber' => 0,
                'userbonstatus_id' => 1,
            ]);
            $openUserbon->orderproducts()->detach();
            $bar2->advance();
        }
        $bar2->finish();
        $this->info('Finished!');
    }
}
