<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:check:transactions {--accept=} {--hasScheduled=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check transaction';

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
        $accept = $this->option('accept');
        $hasScheduled = (bool) $this->option('hasScheduled');

        $transactions = Transaction::query()
            ->where('paymentmethod_id', config('constants.PAYMENT_METHOD_ONLINE'))
            ->whereNotNull('transactionID')
            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_UNSUCCESSFUL'));

        $transactions = $transactions->get();
        $totalCount = $transactions->count();
        if ($totalCount == 0) {
            $this->info('No corrupted transactions found!');
            return 0;
        }

        $this->info('Number of available items: '.$totalCount);

        if (!$hasScheduled && !$this->confirm('Do you wish to continue?', true)) {
            return 0;
        }

        $bar = $this->output->createProgressBar($totalCount);
        $bar->start();
        $counter = 0;
        foreach ($transactions as $transaction) {
            /** @var Transaction $transaction */
            $counter++;
            $this->info("\n");
            $this->warn($counter.' . Transaction '.$transaction->id.' was detected');
            $this->warn('Transaction link => '.action('Web\TransactionController@edit', $transaction));

            if (empty($accept)) {
                $bar->advance();
                continue;
            }

            Log::channel('checkTransactions')->warning('Transaction '.$transaction->id.' was detected');

            $updateResult = $transaction->update([
                'transactionstatus_id' => config('constants.TRANSACTION_STATUS_SUCCESSFUL'),
            ]);

            if ($updateResult) {
                $this->info('Transaction '.$transaction->id.' was updated successfully');
                Log::channel('checkTransactions')->info('Transaction '.$transaction->id.' was updated successfully');
                $bar->advance();
                continue;
            }

            $this->error('Error on updating transaction '.$transaction->id);
            Log::channel('checkTransactions')->error('Error on updating transaction '.$transaction->id);

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n");

        if ($counter == 0) {
            $this->info('No corrupted transactions found');
        }

        $this->info('Done!');

        return 0;
    }
}
