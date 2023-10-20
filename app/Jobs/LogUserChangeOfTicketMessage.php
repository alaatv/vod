<?php

namespace App\Jobs;

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

class LogUserChangeOfTicketMessage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticketMessage;
    private $authUser;
    private $newUserId;
    private $oldUserId;

    /**
     * LogUserOfTicketMessageChange constructor.
     *
     * @param $ticketMessage
     * @param $authUser
     * @param $newUserId
     * @param $oldUserId
     */
    public function __construct(TicketMessage $ticketMessage, User $authUser, int $newUserId, int $oldUserId)
    {
        $this->ticketMessage = $ticketMessage;
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
        TicketActionLogRepo::new($this->ticketMessage->ticket_id, $this->ticketMessage->id, $this->authUser->id,
            TicketAction::CHANGE_USER_OF_MESSAGE_TICKET, $this->oldUserId, $this->newUserId);
    }
}
