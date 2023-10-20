<?php

namespace App\Jobs;

use App\Models\TicketAction;
use App\Models\TicketMessage;
use App\Repositories\TicketActionLogRepo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogInsertingTicketMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var TicketMessage
     */
    private $ticketMessage;
    /**
     * @var int $authUserId
     */
    private $authUserId;

    /**
     * LogInsertingTicketMessage constructor.
     *
     * @param  TicketMessage  $ticketMessage
     * @param  int  $authUserId
     */
    public function __construct(TicketMessage $ticketMessage, int $authUserId)
    {
        $this->ticketMessage = $ticketMessage;
        $this->authUserId = $authUserId;
    }


    /**
     *
     * /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $action = TicketAction::CREATE_TICKET_MESSAGE;
        if ($this->ticketMessage->is_private) {
            $action = TicketAction::CREATE_TICKET_PRIVATE_MESSAGE;
        }
        TicketActionLogRepo::new($this->ticketMessage->ticket_id, $this->ticketMessage->id, $this->authUserId, $action);
    }
}
