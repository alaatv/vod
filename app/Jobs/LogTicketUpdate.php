<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\TicketAction;
use App\Models\User;
use App\Repositories\TicketActionLogRepo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogTicketUpdate implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $newTicket;
    private $authUser;

    /**
     * Create a new job instance.
     *
     * @param  Ticket  $newTicket
     * @param  User  $authUser
     */
    public function __construct(Ticket $newTicket, User $authUser)
    {
        $this->newTicket = $newTicket;
        $this->authUser = $authUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        TicketActionLogRepo::new($this->newTicket->id, null, $this->authUser->id, TicketAction::UPDATE_TICKET);
    }
}
