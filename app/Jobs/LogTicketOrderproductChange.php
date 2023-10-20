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

class LogTicketOrderproductChange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticket;
    private $authUser;
    private $newOrderproductId;
    private $oldOrderproductId;

    /**
     * LogTicketOrderproductChange constructor.
     *
     * @param $ticket
     * @param $authUser
     * @param $newOrderproductId
     * @param $oldOrderproductId
     */
    public function __construct(Ticket $ticket, User $authUser, ?int $newOrderproductId, ?int $oldOrderproductId)
    {
        $this->ticket = $ticket;
        $this->authUser = $authUser;
        $this->newOrderproductId = $newOrderproductId;
        $this->oldOrderproductId = $oldOrderproductId;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        TicketActionLogRepo::new($this->ticket->id, null, $this->authUser->id,
            TicketAction::CHANGE_ORDERPRODUCT_OF_TICKET, $this->oldOrderproductId, $this->newOrderproductId);
    }
}
