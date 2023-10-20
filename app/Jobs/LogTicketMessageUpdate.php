<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\TicketMessage;
use App\Models\User;
use App\Repositories\TicketActionLogRepo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogTicketMessageUpdate implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $newTicketMessage;
    private $user;

    /**
     * Create a new job instance.
     *
     * @param  Ticket  $newTicketMessage
     * @param  Ticket  $oldTicketMessage
     * @param  User  $user
     */
    public function __construct(TicketMessage $newTicketMessage, User $user)
    {
        $this->newTicketMessage = $newTicketMessage;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        TicketActionLogRepo::new(optional(optional($this->newTicketMessage)->ticket)->id,
            optional($this->newTicketMessage)->id, $this->user->id, TicketAction::UPDATE_TICKET_MESSAGE);
    }
}
