<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\User;
use App\Repositories\TicketActionLogRepo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogTicketStatusChange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticket;
    private $authUser;
    private $newStatus;
    private $oldStatus;

    /**
     * LogTicketStatusChange constructor.
     *
     * @param $ticket
     * @param $authUser
     * @param $newStatus
     * @param $oldStatus
     */
    public function __construct(Ticket $ticket, User $authUser, ?string $newStatus, ?string $oldStatus)
    {
        $this->ticket = $ticket;
        $this->authUser = $authUser;
        $this->newStatus = $newStatus;
        $this->oldStatus = $oldStatus;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        TicketActionLogRepo::new($this->ticket->id, null, $this->authUser->id, TicketAction::CHANGE_STATUS_OF_TICKET,
            $this->oldStatus, $this->newStatus);
    }
}
