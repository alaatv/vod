<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class Eidi98 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:eidi98';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gives gift credit to users - Norouz 98';

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
        $users = User::all();
        if (!$this->confirm('Giving `Eidi` to  '.$users->count().' users. Do you wish to continue?', true)) {
            return 0;
        }
        $bar = $this->output->createProgressBar($users->count());
        foreach ($users as $user) {
            $user->deposit(30000, 2);
            $bar->advance();
        }
        $bar->finish();
        $this->info('Done');
    }
}
