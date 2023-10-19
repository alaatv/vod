<?php

namespace App\Console\Commands;

use App\Models\_3aExam;
use App\Models\Order;
use App\Traits\APIRequestCommon;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AddOrderExamTo3aCommand extends Command
{
    use APIRequestCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:orderExam {--interval= : filter orders by days} {--user= : filter users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Exam With Request To 3a';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $interval = $this->option('interval');
        $userId = $this->option('user');

        $this->info('getting orderproducts...');
        $_3AOrderproducts = Order::with('orderproducts')->whereHas('orderproducts', function ($q) {
            $q->whereIn('product_id', _3aExam::pluck('product_id'));
        })->when($userId, function ($query, $userId) {
            $query->where('user_id', $userId);
        })->when($interval, function ($query, $interval) {
            $query->where('completed_at', '>=', Carbon::now()->subDays($interval));
        })->get()->pluck('orderProducts')->flatten();

        $count = $_3AOrderproducts->count();
        $this->info("$count orderproducts found");
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach ($_3AOrderproducts as $orderproduct) {
            $user = $orderproduct->order->user;
            $exams = _3aExam::productId($orderproduct->product_id)->get();
            foreach ($exams as $exam) {
                $result = $this->register3ARequest($user, $exam->id);
                if (!$result) {
                    Log::error('Product '.$orderproduct->product_id.', Exam '.$exam->id.' was not registered for user '.$user->id);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info('Done!');
    }
}
