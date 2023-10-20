<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\WalletCreditGiven;
use App\Traits\CharacterCommon;
use App\Traits\User\AssetTrait;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GiveSubscriptoinWalletCredit implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    use CharacterCommon;
    use AssetTrait;


    /**
     *
     * @var array
     */
    private $array;

    /**
     *
     * @var User
     */
    private $user;

    /**
     *
     * @var User
     */
    private $credit;

    /**
     * InsertKMTUsers constructor.
     *
     * @param  User  $user
     * @param  int  $credit
     */
    public function __construct(User $user, int $credit)
    {
        $this->user = $user;
        $this->credit = $credit;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $depositResult = $this->user->deposit($this->credit, config('constants.WALLET_TYPE_GIFT'));
        if ($depositResult['result']) {
            $this->user->notify(new WalletCreditGiven($this->credit));
            return null;
        }

        Log::channel('giveWalletCredit')->info('Error on giving credit to user: '.$this->user->id);

        return null;
    }
}
