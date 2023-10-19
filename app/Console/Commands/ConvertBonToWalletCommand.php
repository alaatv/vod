<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Userbon;
use App\Notifications\BonToWallet;
use Illuminate\Console\Command;

class ConvertBonToWalletCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:convert:bon:to:wallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts user bons to wallet credit';

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
        $users = User::whereHas('userbons', function ($q) {
            $q->where('userbonstatus_id', config('constants.USERBON_STATUS_ACTIVE'));
        })->get();
        $userCount = $users->count();

        if (!$this->confirm("There are $userCount users, Would you like to continue?", true)) {
            return 0;
        }
        $this->info('Converting userbons to wallet credit ...');
        $this->info("\n\n");

        $bar = $this->output->createProgressBar($userCount);
        $users->load('userbons');
        foreach ($users as $user) {
            $activeUserbons =
                $user->userbons->where('userbonstatus_id', config('constants.USERBON_STATUS_ACTIVE'));
            $activeUserbonsCount = $activeUserbons->sum('totalNumber');
            if ($activeUserbonsCount >= 20) {
                $prefix = 350;
            } else {
                $prefix = 500;
            }

            $amount = $prefix * $activeUserbonsCount;
            /** @var User $user */
            $depositResult = $user->deposit($amount, config('constants.WALLET_TYPE_GIFT'));
            if ($depositResult['result']) {
                $user->notify(new BonToWallet($activeUserbonsCount, $amount));
                /** @var Userbon $activeUserbon */
                foreach ($activeUserbons as $activeUserbon) {
                    $activeUserbon->update([
                        'usedNumber' => $activeUserbon->totalNumber,
                        'userbonstatus_id' => config('constants.USERBON_STATUS_USED'),
                    ]);
                }
            } else {
                $this->info('User #'.$user->id.' did not get credit');
                $this->info("\n\n");
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info('Finished!');

    }
}
