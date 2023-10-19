<?php

namespace App\Console\Commands;

use App\Models\SMS;
use App\Repositories\SmsBlackListRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FillBlackListBaseReceivedMsgCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:fill:black:list';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'put mobiles with disabling sms request in black list';
    /**
     * @var SMS[]|Builder[]|Collection
     */
    private array|Collection $SMSs;

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
    public function handle(): int
    {
        $this->findDisablingRequestMessages()
            ->addThemToBlackList();

        return 0;
    }

    private function addThemToBlackList(): void
    {
        if (!$this->confirm($this->SMSs->count().' Disabling Message Received, Do You Want To Continue?')) {
            return;
        }

        $progressBar = $this->output->createProgressBar($this->SMSs->count());
        $progressBar->start();

        foreach ($this->SMSs as $sms) {
            $this->tryAddToBlackList($sms);
            $progressBar->advance();
        }

        $progressBar->finish();
    }

    private function tryAddToBlackList(SMS $SMS)
    {
        SmsBlackListRepository::create(['mobile' => $SMS->from]);
    }

    private function findDisablingRequestMessages(): static
    {
        $this->SMSs = SMS::received()
            ->where('created_at', '>=', '2022-02-07 20:30:00')
            ->where(function ($query) {
                $query->where('message', 'like', '%لغو%')
                    ->orWhere('message', '1');
            })->get()
            ->unique('from');

        return $this;
    }
}
