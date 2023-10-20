<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ZeroWalletsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:zero:wallets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'zeroes wallet credits';

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
        $wallets = Wallet::where('balance', '>', 0)
            ->where('wallettype_id', config('constants.WALLET_TYPE_GIFT'))->get();

        $walletCount = $wallets->count();
        if (!$this->confirm("$walletCount wallets found , Do you want to continue zeroing these wallets", true)) {
            return 0;
        }
        $bar = $this->output->createProgressBar($walletCount);
        $completedWallets = 0;
        $failedWallets = 0;
        foreach ($wallets as $wallet) {
            $transaction = $wallet->transactions()
                ->create([
                    'wallet_id' => $wallet->id,
                    'cost' => $wallet->balance,
                    'transactionstatus_id' => config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
                    'paymentmethod_id' => config('constants.PAYMENT_METHOD_WALLET'),
                    'completed_at' => Carbon::now('Asia/Tehran'),
                    'managerComment' => 'تراکنش سیستمی . صفر کردن کیف پول ها در 13 مهر 1398',
                    'description' => 'پایان اعتبار کیف پول',
                ]);

            if (isset($transaction)) {
                $wallet->update([
                    'balance' => 0,
                    'pending_to_reduce' => 0,
                ]);
                $completedWallets++;
            } else {
                $this->info('Wallet #'.$wallet->id.' could not be updated');
                $this->info("\n\n");
                $failedWallets++;
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info("\n\n");
        $this->info($completedWallets.' wallets zeroed successfully');
        $this->info("\n\n");
        $this->info($failedWallets.' wallets failed');
        $this->info("\n\n");
    }
}
