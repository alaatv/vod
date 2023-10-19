<?php

namespace App\Console\Commands;

use App\Models\Bon;
use App\Models\Lottery;
use App\Models\User;
use App\Models\Userbon;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use LuckyBox\Card\IdCard;
use LuckyBox\LuckyBox;

class HoldLotteryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:holdLottery {lottery}';

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
        $lotteryName = $this->argument('lottery');
        $lottery = Lottery::where('name', $lotteryName)->first();
        if (!isset($lottery)) {
            $this->error('Lottery not found!');
            return 0;
        }

        $rankCounter = 0;
        $successCounter = 0;
        $failedCounter = 0;
        $warningCounter = 0;

        switch ($lotteryName) {
            case 'yalda1400_2':
                $numberOfWinners = 2;
                break;
            default:
                $this->error('Invalid lottery');
                return null;

        }

        $bon = Bon::query()->where('name', config('constants.BON2'))->first();
        if (!isset($bon)) {
            $this->error('Bon not found!');
            return 0;
        }

        $luckyBox = new LuckyBox();
        $luckyBox->setConsumable(true);

        $participants = Userbon::query()
            ->where('bon_id', $bon->id)
            ->where('userbonstatus_id', config('constants.USERBON_STATUS_ACTIVE'))
            ->get();

        $participantsCount = $participants->count();
        if (!$this->confirm("$participantsCount found with total points of ".$participants->sum('totalNumber').' , Do you wish to give them lottery cards?',
            true)) {
            $this->info('Operation aborted!');
            return 0;
        }

        $this->info('Giving lottery cards...');
        $participantsBar = $this->output->createProgressBar($participantsCount);
        $cardCounter = 0;
        foreach ($participants as $participant) {
            $points = $participant->totalNumber - $participant->usedNumber;
            for ($i = $points; $i > 0; $i--) {
                $card = new IdCard();
                $card->setId($participant->user->id)->setRate(100);
                $luckyBox->add($card);
                $cardCounter++;
            }
            $participantsBar->advance();
        }
        $participantsBar->finish();
        $this->info("\n");


        if (!$this->confirm('Cards were given to the participants, do you wish to hold the lottery?', true)) {
            $this->info('Operation aborted!');
            return 0;

        }

        $lotteryBar = $this->output->createProgressBar($cardCounter);
        $winners = [];
        $winnerCounter = 0;
        while (!$luckyBox->isEmpty()) {
            $card = $luckyBox->draw();
            $cardId = $card->getId();

            $user = User::find($cardId);
            if (!isset($user)) {
                Log::channel('holdLotteryErrors')->error("User of $cardId was not found!");
                $failedCounter++;
                $lotteryBar->advance();
                continue;
            }

            $userPoints = $participants->where('user_id', $user->id);//userbon

            if ($userPoints->isEmpty()) {
                Log::channel('holdLotteryErrors')->error('No points found for user '.$user->id);
                $failedCounter++;
                $lotteryBar->advance();
                continue;
            }

            $totalUserPoints = $userPoints->sum('totalNumber');
            foreach ($userPoints as $userPoint) {
                $userPoint->update([
                    'userbonstatus_id' => config('constants.USERBON_STATUS_USED'),
                    'usedNumber' => $userPoint->totalNumber,
                ]);
            }

            if (in_array($cardId, $winners)) {
                $lotteryBar->advance();
                continue;
            }

            $userlotteries = $user->lotteries->where('lottery_id', $lottery->id);
            if ($userlotteries->isEmpty()) {
                $rankCounter++;
                $user->lotteries()->attach($lottery->id, ['rank' => $rankCounter]);
                Log::channel('holdLotteryInfo')->info("#$rankCounter => user id: ".$user->id.'with '.$totalUserPoints.' points');
                $successCounter++;
                $winnerCounter++;
                array_push($winners, $cardId);
                $lotteryBar->advance();
                if ($winnerCounter > $numberOfWinners) {
                    continue;
                }

                $this->info("\n");
                $this->info('Winner #'.$winnerCounter.' is user '.$user->id.' with '.$totalUserPoints.' points');
                if ($this->confirm('Enter any key to continue', true)) {
                    if ($winnerCounter == $numberOfWinners) {
                        $this->info("Determining other users' rank ...");
                    }
                    continue;
                }
                continue;
            }

            if ($userlotteries->first()->pivot->rank == 0) {
                Log::channel('holdLotteryWarnings')->warning('User '.$user->id.' had been removed from the lottery');
                $warningCounter++;
                $lotteryBar->advance();
                continue;
            }

            Log::channel('holdLotteryErrors')->error('User '.$user->id.' had been participated in lottery before with the rank of more than zero');
            $failedCounter++;
            $lotteryBar->advance();
        }

        $lotteryBar->finish();
        $this->info("\n");

        $this->info('Number of successfully processed winners: '.$successCounter);
        $this->info('Last winner: '.$rankCounter);
        $this->info('Number of failed winners: '.$failedCounter);
        $this->info('Number of warnings: '.$warningCounter);
        $this->info('DONE!');
        $this->info('Please check these logs:');
        $this->info('holdLotteryErrors : for errors');
        $this->info('holdLotteryWarnings : for warnings');
        $this->info('holdLotteryInfo : for the list of winners');
        return 0;
    }
}
