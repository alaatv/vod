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

class LogTicketDepartmentChange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $ticket;
    private $authUser;
    private $newDepartment;
    private $oldDepartment;

    /**
     * LogTicketDepartmentChange constructor.
     *
     * @param $ticket
     * @param $authUser
     * @param $newDepartment
     * @param $oldDepartment
     */
    public function __construct(Ticket $ticket, User $authUser, ?string $newDepartment, ?string $oldDepartment)
    {
        $this->ticket = $ticket;
        $this->authUser = $authUser;
        $this->newDepartment = $newDepartment;
        $this->oldDepartment = $oldDepartment;
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
            TicketAction::CHANGE_DEPARTMENT_OF_TICKET, $this->oldDepartment, $this->newDepartment);
    }
}
