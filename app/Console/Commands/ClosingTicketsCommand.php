<?php

namespace App\Console\Commands;

use App\Classes\TicketChangeLogger;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\User;
use App\Repositories\TicketRepo;
use App\Traits\UserCommon;
use Illuminate\Console\Command;

class ClosingTicketsCommand extends Command
{
    use UserCommon;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:ticket:closing {since} {till}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Closing tickets from one date to another date';

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
        $since = $this->argument('since');
        $till = $this->argument('till');


        $tickets = TicketRepo::getUnAnsweredTickets([
            'department_id' => TicketDepartment::ACCOUNT_TRANSFER,
            'since' => $since,
            'till' => $till
        ])->get();

        $count = $tickets->count();

        if (!$this->confirm("$count tickets found, continue?")) {
            return false;
        }

        $this->info('Closing tickets started ...');

        $admin = User::find(947310);

        if (!$admin) {
            $this->info('Admin user not found');
            return false;
        }

        /** @var Ticket $ticket */


        $bar = $this->output->createProgressBar($count);
        $bar->start();
        foreach ($tickets as $ticket) {
            $oldTicket = clone $ticket;
            $ticket->close();

            $ticket = $ticket->fresh();
            (new TicketChangeLogger($oldTicket, $ticket, $admin))->log();

            $bar->advance();
        }

        $bar->finish();
        $this->info('Done!');

        return false;
    }
}
