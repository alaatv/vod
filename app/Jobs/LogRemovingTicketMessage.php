<?php

namespace App\Jobs;

use App\Models\TicketAction;
use App\Models\TicketAction;
use App\Models\TicketMessage;
use App\Repositories\TicketActionLogRepo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogRemovingTicketMessage implements ShouldQueue
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
        TicketActionLogRepo::new($this->ticketMessage->ticket_id, $this->ticketMessage->id, $this->authUserId,
            TicketAction::REMOVE_TICKET_MESSAGE);
    }
}
