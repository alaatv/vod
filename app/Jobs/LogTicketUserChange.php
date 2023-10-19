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

class LogTicketUserChange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticket;
    private $authUser;
    private $newUserId;
    private $oldUserId;

    /**
     * LogTicketUserChange constructor.
     *
     * @param  Ticket  $ticket
     * @param  User  $authUser
     * @param  int  $newUserId
     * @param  int  $oldUserId
     */
    public function __construct(Ticket $ticket, User $authUser, ?int $newUserId, ?int $oldUserId)
    {
        $this->ticket = $ticket;
        $this->authUser = $authUser;
        $this->newUserId = $newUserId;
        $this->oldUserId = $oldUserId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        TicketActionLogRepo::new($this->ticket->id, null, $this->authUser->id, TicketAction::CHANGE_USER_OF_TICKET,
            $this->oldUserId, $this->newUserId);
    }
}
