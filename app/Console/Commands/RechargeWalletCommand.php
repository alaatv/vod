<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\WalletCreditGiven;
use Illuminate\Console\Command;

class RechargeWalletCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:wallet:recharge {amount : how much do you charge specific users\' wallet ?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Charge wallet amount for specific users';

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

        $amount = $this->argument('amount');

        $usersCredentials = [
            [
                'nationalCode' => '4120191117',
                'mobile' => '09165504357',
            ],
            [
                'nationalCode' => '1881183793',
                'mobile' => '09160540847',
            ],
            [
                'nationalCode' => '0926387898',
                'mobile' => '09385265970',
            ],
            [
                'nationalCode' => '0200453882',
                'mobile' => '09198829152',
            ],
            [
                'nationalCode' => '0372156320',
                'mobile' => '09016580026',
            ],
            [
                'nationalCode' => '4510195346',
                'mobile' => '09187965001',
            ],
            [
                'nationalCode' => '0372544975',
                'mobile' => '09127478132',
            ],
            [
                'nationalCode' => '0550316671',
                'mobile' => '09182944424',
            ],
            [
                'nationalCode' => '0372651331',
                'mobile' => '09370859189',
            ],
            [
                'nationalCode' => '1940840279',
                'mobile' => '09216410394',
            ],
            [
                'nationalCode' => '0441094333',
                'mobile' => '09106837044',
            ],
            [
                'nationalCode' => '0550319281',
                'mobile' => '09182563432',
            ],
            [
                'nationalCode' => '3540228705',
                'mobile' => '09172918981',
            ],
            [
                'nationalCode' => '4311772025',
                'mobile' => '09330711703',
            ],
            [
                'nationalCode' => '0590560077',
                'mobile' => '09335232101',
            ],
            [
                'nationalCode' => '4560335281',
                'mobile' => '09102885191',
            ],
            [
                'nationalCode' => '1540690628',
                'mobile' => '09147657825',
            ],
            [
                'nationalCode' => '2283821568',
                'mobile' => '09915766048',
            ],
            [
                'nationalCode' => '0372610951',
                'mobile' => '09914079565',
            ],
            [
                'nationalCode' => '4260366009',
                'mobile' => '09172763953',
            ],
            [
                'nationalCode' => '4560310084',
                'mobile' => '09100334841',
            ],
            [
                'nationalCode' => '6660456538',
                'mobile' => '09353801342',
            ],
            [
                'nationalCode' => '0250076004',
                'mobile' => '09386167944',
            ],
            [
                'nationalCode' => '0025530402',
                'mobile' => '09142911249',
            ],
            [
                'nationalCode' => '0150083173',
                'mobile' => '09199805267',
            ],
            [
                'nationalCode' => '0630481717',
                'mobile' => '09337514344',
            ],
            [
                'nationalCode' => '0520417453',
                'mobile' => '09309480530',
            ],
            [
                'nationalCode' => '1180110481',
                'mobile' => '09913930712',
            ],
            [
                'nationalCode' => '0150319231',
                'mobile' => '09015216721',
            ],
            [
                'nationalCode' => '0781097819',
                'mobile' => '09156569477',
            ],
            [
                'nationalCode' => '1741187389',
                'mobile' => '09027384404',
            ],
            [
                'nationalCode' => '2791069453',
                'mobile' => '09114814287',
            ],
            [
                'nationalCode' => '3242772830',
                'mobile' => '09030836425',
            ],
            [
                'nationalCode' => '2830490381',
                'mobile' => '09142474210',
            ],
            [
                'nationalCode' => '5080181710',
                'mobile' => '09196585058',
            ],
            [
                'nationalCode' => '0456789015',
                'mobile' => '09376497736',
            ],
            [
                'nationalCode' => '0452902363',
                'mobile' => '09153515220',
            ],
        ];

        $count = count($usersCredentials);

        if (!$this->confirm("$count credentials found, Would you like to continue?")) {
            $this->info('Aborted');
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($usersCredentials as $usersCredential) {
            $user = User::query()->where('mobile', $usersCredential['mobile'])
                ->where('nationalCode', $usersCredential['nationalCode'])
                ->first();

            if (!isset($user)) {
                $this->info('User not found : mobile: '.$usersCredential['mobile'].' , nationalCode: '.$usersCredential['nationalCode']);
                $bar->advance();
                continue;
            }

            $user->deposit($amount);

            $user->notify(new WalletCreditGiven($amount));

            $bar->finish();

            $this->info('Done!');
        }
    }
}
