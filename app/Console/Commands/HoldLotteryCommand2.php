<?php

namespace App\Console\Commands;

use App\Models\Bon;
use App\Models\Lottery;
use App\Models\LotteryStatus;
use App\Models\User;
use App\Models\Userbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use LuckyBox\Card\IdCard;
use LuckyBox\LuckyBox;

class HoldLotteryCommand2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:holdLottery2 {id}';

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
        try {
            $lottery = Lottery::find($this->argument('id'));
            if (!isset($lottery)) {
                $this->error('Lottery not found!');
                return null;
            }

            $rankCounter = 0;
            $successCounter = 0;
            $failedCounter = 0;
            $warningCounter = 0;

            switch ($lottery->name) {
                case 'raheAbrishamYalda99':
                    $numberOfWinners = 1;
                    break;
                case 'rahBaladYalda99':
                    $numberOfWinners = 3;
                    break;
                default:
                    $this->error('Invalid lottery');
                    return null;

            }

            $bon = Bon::query()->where('name', config('constants.BON2'))->first();
            if (!isset($bon)) {
                $this->error('Bon not found!');
                return null;
            }

            $luckyBox = new LuckyBox();
            $luckyBox->setConsumable(true);

            $participants = Userbon::query()
                ->where('bon_id', $bon->id)
                ->where('userbonstatus_id', config('constants.USERBON_STATUS_ACTIVE'))
                ->get();

            $participantsCount = $participants->count();

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
            $lottery->lottery_status_id = LotteryStatus::HOLDED;
            $lottery->save();
        } catch (Exception $exception) {
            $lottery->lottery_status_id = LotteryStatus::REPORT_HOLDING_ERROR;
            $lottery->save();

            Log::channel('holdLotteryErrors')
                ->warning(
                    'holdLotteryErrors: errorFile='.
                    $exception->getFile().
                    ', errorLine='.
                    $exception->getLine().
                    ', errorMessage='.
                    $exception->getMessage()
                );
        }

        return null;
    }
}
