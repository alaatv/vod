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

class LogTicketMessageBodyChange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticketMessage;
    private $authUser;
    private $newBody;
    private $oldBody;

    /**
     * LogTicketMessageBodyChange constructor.
     *
     * @param $ticketMessage
     * @param $authUser
     * @param $newBody
     * @param $oldBody
     */
    public function __construct(TicketMessage $ticketMessage, User $authUser, ?string $newBody, ?string $oldBody)
    {
        $this->ticketMessage = $ticketMessage;
        $this->authUser = $authUser;
        $this->newBody = $newBody;
        $this->oldBody = $oldBody;
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
            TicketAction::CHANGE_BODY_OF_MESSAGE_TICKET, $this->oldBody, $this->newBody);
    }
}
