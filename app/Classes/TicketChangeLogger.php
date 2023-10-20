<?php


namespace App\Classes;


use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\User;
use App\Repositories\TicketActionLogRepo;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class TicketChangeLogger
{

    private $oldTicket;
    private $newTicket;
    private $authUser;

    /**
     * TticketChangeLogger constructor.
     *
     * @param $oldTicket
     * @param $newTicket
     * @param $authUser
     */
    public function __construct(Ticket $oldTicket, Ticket $newTicket, User $authUser)
    {
        $this->oldTicket = $oldTicket;
        $this->newTicket = $newTicket;
        $this->authUser = $authUser;
    }


    public function log()
    {
        try {
            if ($this->oldTicket->title != $this->newTicket->title) {
//            dispatch(new LogTicketTitleChange($this->newTicket , $this->authUser , $this->newTicket->title , $this->oldTicket->title));
                TicketActionLogRepo::new($this->newTicket->id, null, $this->authUser->id,
                    TicketAction::CHANGE_TITLE_OF_TICKET, $this->oldTicket->title, $this->newTicket->title);
            }
        } catch (QueryException $e) {
            $this->logQueryException($e);
        }

        try {
            if ($this->oldTicket->orderproduct_id != $this->newTicket->orderproduct_id) {
//            dispatch(new LogTicketOrderproductChange($this->newTicket , $this->authUser , $this->newTicket->orderproduct_id , $this->oldTicket->orderproduct_id));
                TicketActionLogRepo::new($this->newTicket->id, null, $this->authUser->id,
                    TicketAction::CHANGE_ORDERPRODUCT_OF_TICKET, $this->oldTicket->orderproduct_id,
                    $this->newTicket->orderproduct_id);
            }
        } catch (QueryException $e) {
            $this->logQueryException($e);
        }

        try {
            if ($this->oldTicket->user_id != $this->newTicket->user_id) {
//            dispatch(new LogTicketUserChange($this->newTicket , $this->authUser , $this->newTicket->user_id , $this->oldTicket->user_id));
                TicketActionLogRepo::new($this->newTicket->id, null, $this->authUser->id,
                    TicketAction::CHANGE_USER_OF_TICKET, $this->oldTicket->user_id, $this->newTicket->user_id);
            }
        } catch (QueryException $e) {
            $this->logQueryException($e);
        }

        try {
            $newTicketStatus = optional($this->newTicket->status)->title;
            $oldTicketStatus = optional($this->oldTicket->status)->title;
            if ($oldTicketStatus != $newTicketStatus) {
//            dispatch(new LogTicketStatusChange($this->newTicket , $this->authUser , $newTicketStatus , $oldTicketStatus));
                TicketActionLogRepo::new($this->newTicket->id, null, $this->authUser->id,
                    TicketAction::CHANGE_STATUS_OF_TICKET, $oldTicketStatus, $newTicketStatus);
            }
        } catch (QueryException $e) {
            $this->logQueryException($e);
        }

        try {
            $newTicketDepartment = optional($this->newTicket->department)->title;
            $oldTicketDepartment = optional($this->oldTicket->department)->title;
            if ($oldTicketDepartment != $newTicketDepartment) {
//            dispatch(new LogTicketDepartmentChange($this->newTicket , $this->authUser , $newTicketDepartment , $oldTicketDepartment));
                TicketActionLogRepo::new($this->newTicket->id, null, $this->authUser->id,
                    TicketAction::CHANGE_DEPARTMENT_OF_TICKET, $oldTicketDepartment, $newTicketDepartment);
            }
        } catch (QueryException $e) {
            $this->logQueryException($e);
        }

        try {
            $newTicketPriority = optional($this->newTicket->priority)->title;
            $oldTicketPriority = optional($this->oldTicket->priority)->title;
            if ($oldTicketPriority != $newTicketPriority) {
//            dispatch(new LogTicketPriorityChange($this->newTicket , $this->authUser , $newTicketPriority , $oldTicketPriority));
                TicketActionLogRepo::new($this->newTicket->id, null, $this->authUser->id,
                    TicketAction::CHANGE_PRIORITY_OF_TICKET, $oldTicketPriority, $newTicketPriority);
            }
        } catch (QueryException $e) {
            $this->logQueryException($e);
        }
    }

    private function logQueryException(Exception $e)
    {
        Log::channel('ticketLog')->error('Error on inserting log for ticket: '.$this->newTicket->id);
        Log::channel('ticketLog')->error('error: '.$e->getMessage());
        Log::channel('ticketLog')->error('file: '.$e->getFile());
        Log::channel('ticketLog')->error('line: '.$e->getLine());
    }
}
