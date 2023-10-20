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

class LogTicketTitleChange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticket;
    private $authUser;
    private $newTitle;
    private $oldTitle;

    /**
     * LogTicketTitleChange constructor.
     *
     * @param $ticket
     * @param $authUser
     * @param $newTitle
     * @param $oldTitle
     */
    public function __construct(Ticket $ticket, User $authUser, string $newTitle, string $oldTitle)
    {
        $this->ticket = $ticket;
        $this->authUser = $authUser;
        $this->newTitle = $newTitle;
        $this->oldTitle = $oldTitle;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        TicketActionLogRepo::new($this->ticket->id, null, $this->authUser->id, TicketAction::CHANGE_TITLE_OF_TICKET,
            $this->oldTitle, $this->newTitle);
    }
}
