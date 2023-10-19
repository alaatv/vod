<?php

namespace App\Jobs;

use App\Models\TicketAction;
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

class LogTicketChangeOfTicketMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticketMessage;
    private $authUser;
    private $newTicketId;
    private $oldTicketId;

    /**
     * LogTicketIdOfTicketMessageChange constructor.
     *
     * @param $ticketMessage
     * @param $authUser
     * @param $newTicketId
     * @param $oldTicketId
     */
    public function __construct(TicketMessage $ticketMessage, User $authUser, int $newTicketId, int $oldTicketId)
    {
        $this->ticketMessage = $ticketMessage;
        $this->authUser = $authUser;
        $this->newTicketId = $newTicketId;
        $this->oldTicketId = $oldTicketId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        TicketActionLogRepo::new($this->ticketMessage->ticket_id, $this->ticketMessage->id, $this->authUser->id,
            TicketAction::CHANGE_TICKET_OF_TICKET_MESSAGE, $this->oldTicketId, $this->newTicketId);
    }
}
