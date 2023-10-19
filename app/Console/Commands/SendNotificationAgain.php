<?php

namespace App\Console\Commands;

use App\Jobs\ResendUnsuccessfulMessage;
use App\Repositories\SmsDetailsRepository;
use Illuminate\Console\Command;

class SendNotificationAgain extends Command
{
    protected $signature = 'AlaaTV:sendNotificationAgain {pattern_code} {--from=} {--till=}';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(SmsDetailsRepository $smsDetailsRepository)
    {
        $patternCode = $this->argument('pattern_code');
        $createdAt = $this->option('from');
        $createdTill = $this->option('till');
        $smsDetails = $smsDetailsRepository
            ->filterBy(['pattern_code' => $patternCode, 'sms_result_id' => 2]);

        if (isset($createdAt)) {
            $smsDetails->createdAfter($createdAt);
        }

        if (isset($createdTill)) {
            $smsDetails->createdBefore($createdTill);
        }

        $smsDetails = $smsDetails->with(['sms'])->get();

        $steps = $smsDetails->count();

        if (!$this->getConfirmation($steps)) {
            return 0;
        }

        $bar = $this->output->createProgressBar($steps);
        $bar->start();
        foreach ($smsDetails as $smsDetail) {
            dispatch(new ResendUnsuccessfulMessage($smsDetail->sms, [], 'default2'));
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }

    private function getConfirmation($steps): bool
    {
        $this->info("$steps item found.");
        return $this->confirm('Do you want to continue? ', true);
    }

}
