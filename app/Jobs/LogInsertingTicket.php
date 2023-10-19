<?php

namespace App\Jobs;

use App\Models\TicketAction;
use App\Models\TicketAction;
use App\Repositories\TicketActionLogRepo;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogInsertingTicket implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var int $ticketId
     */
    private $ticketId;
    /**
     * @var int $authUserId
     */
    private $authUserId;

    /**
     * LogInsertingTicket constructor.
     *
     * @param  int  $ticketId
     * @param  int  $authUserId
     */
    public function __construct(int $ticketId, int $authUserId)
    {
        $this->ticketId = $ticketId;
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
        TicketActionLogRepo::new($this->ticketId, null, $this->authUserId, TicketAction::CREATE_TICKET);
    }
}
