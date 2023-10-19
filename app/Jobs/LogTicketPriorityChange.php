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

class LogTicketPriorityChange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticket;
    private $authUser;
    private $newPriority;
    private $oldPriority;

    /**
     * LogTicketPriorityChange constructor.
     *
     * @param $ticket
     * @param $authUser
     * @param $newPriority
     * @param $oldPriority
     */
    public function __construct(Ticket $ticket, User $authUser, ?string $newPriority, ?string $oldPriority)
    {
        $this->ticket = $ticket;
        $this->authUser = $authUser;
        $this->newPriority = $newPriority;
        $this->oldPriority = $oldPriority;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        TicketActionLogRepo::new($this->ticket->id, null, $this->authUser->id, TicketAction::CHANGE_PRIORITY_OF_TICKET,
            $this->oldPriority, $this->newPriority);
    }
}
