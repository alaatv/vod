<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\Marketing4;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;

class SendSMSFromExcelCommand extends Command
{
    protected $signature = 'alaaTv:aban3AParticipants';
    protected $description = 'send message to Aban 1400 3a participants';
    private array $files;
    private $mobiles;
    private $users;

    public function __construct()
    {
        parent::__construct();
        $this->users = collect();
        $this->mobiles = collect();
    }


    public function handle()
    {
        $this->loadFiles()->fillUsers()->sendNotif();

        return Command::SUCCESS;
    }

    public function sendNotif()
    {
        $bar = $this->output->createProgressBar(count($this->users->flatten()));
        $this->info("\nSending Notifications:\n");
        $bar->start();

        foreach ($this->users->flatten() as $user) {
            $bar->advance();
            $this->info("user {$user->id}");

            $user->notify(new Marketing4());
            $user->deposit(59000, config('constants.WALLET_TYPE_GIFT'));
        }

        $bar->finish();
    }

    public function fillUsers()
    {
        $bar = $this->output->createProgressBar(count($this->files));

        $this->info("Processing files\n");
        $bar->start();

        foreach ($this->files as $key => $file) {
            $bar->advance();
            $this->info("  Process file {$file->getFileName()} ... ");

            $this->processFile($file);
        }

        $bar->finish();

        $users = $this->findUsers()->flatten();
        $this->users->push($users);

        return $this;
    }

    private function processFile($file)
    {
        $this->mobiles->push($this->extractMobiles($file));
    }

    private function extractMobiles($file)
    {
        $data = Excel::toCollection(new UsersImport(), $file)->flatten();
        $mobiles = [];
        foreach ($data as $row) {
            if (intval($row)) {
                $mobiles[] = $row;
            }
        }
        return $mobiles;
    }

    private function findUsers()
    {
        return User::whereIn('mobile', $this->mobiles->flatten()->toArray())
            ->whereHas('orders', function ($q) {
                $q->where('orderstatus_id', Order::getDoneOrderStatus())
                    ->where('paymentstatus_Id', Order::getDoneOrderPaymentStatus())
                    ->whereHas('orderproducts', function ($q2) {
                        $q2->whereIn('product_id', [
                            Product::_3A_JAMBANDI_YAZDAHOM_TAJROBI_MIAN_TERM1_1401,
                            Product::_3A_JAMBANDI_YAZDAHOM_RIYAZI_MIAN_TERM1_1401,
                            Product::_3A_JAMBANDI_YAZDAHOM_ENSANI_MIAN_TERM1_1401,
                            Product::_3A_JAMBANDI_DAVAZDAHOM_TAJROBI_MIAN_TERM1_1401,
                            Product::_3A_JAMBANDI_DAVAZDAHOM_RIYAZI_MIAN_TERM1_1401,
                            Product::_3A_JAMBANDI_DAVAZDAHOM_ENSANI_MIAN_TERM1_1401
                        ]);
                    })
                    ->whereHas('transactions', function ($q3) {
                        $q3->where('cost', '>', 0)
                            ->where('transactionstatus_id', config('constants.TRANSACTION_STATUS_SUCCESSFUL'));
                    });
            })->get();
    }

    public function loadFiles()
    {
        $files = File::files(storage_path().'/app/public/general');
        $this->files = array_filter($files, fn($file) => $file->getExtension() == 'xlsx');

        return $this;
    }

}

class UsersImport implements ToArray
{
    public function array(array $array)
    {
        // TODO: Implement array() method.
    }
}
