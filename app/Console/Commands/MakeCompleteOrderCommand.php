<?php

namespace App\Console\Commands;

use App\Events\SendOrderNotificationsEvent;
use App\Models\User;
use App\Repositories\OrderproductRepo;
use App\Repositories\OrderRepo;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;
use SplFileInfo;

class MakeCompleteOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:make-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'give product to users';

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
        $splFileInfoObject = new SplFileInfo(storage_path('/app/public/general/chabaharnahayi.xlsx'));
        $data = Excel::toArray(new OrderImport(), $splFileInfoObject);
        foreach ($data[0] as $row) {
            $firstName = $row[0];
            $lastName = $row[1];
            $nationalCode = $row[2];
            $mobile = $row[3];
            $user = User::firstOrCreate(
                ['nationalCode' => $nationalCode, 'mobile' => $mobile,],
                [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'userstatus_id' => config('constants.USER_STATUS_ACTIVE'),
                    'photo' => config('constants.PROFILE_IMAGE_PATH').config('constants.PROFILE_DEFAULT_IMAGE'),
                    'password' => bcrypt($nationalCode),
                ]
            );

            $order =
                OrderRepo::createBasicCompletedOrder($user->id, 3, 0, 1400000, 6488, 100, orderStatusId: 2, seller: 1);
            OrderproductRepo::createBasicOrderproduct($order->id, 1007, 1400000, 1400000, 1, 0);
            event(new SendOrderNotificationsEvent($order, $order->user));
        }
        return 0;
    }
}

class OrderImport implements ToArray
{
    public function array(array $array)
    {
    }
}
