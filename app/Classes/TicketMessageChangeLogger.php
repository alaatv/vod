<?php


namespace App\Classes;


use App\Models\TicketAction;
use App\Models\TicketAction;
use App\Models\TicketMessage;
use App\Models\User;
use App\Repositories\TicketActionLogRepo;

class TicketMessageChangeLogger
{
    private $oldMessage;
    private $newMessage;
    private $authUser;

    /**
     * TicketMessageChangeLogger constructor.
     *
     * @param $oldMessage
     * @param $newMessage
     * @param $authUser
     */
    public function __construct(TicketMessage $oldMessage, TicketMessage $newMessage, User $authUser)
    {
        $this->oldMessage = $oldMessage;
        $this->newMessage = $newMessage;
        $this->authUser = $authUser;
    }

    public function log()
    {
        if ($this->oldMessage->body != $this->newMessage->body) {
//            dispatch(new LogTicketMessageBodyChange($this->newMessage , $this->authUser , null , null));
            TicketActionLogRepo::new($this->newMessage->ticket_id, $this->newMessage->id, $this->authUser->id,
                TicketAction::CHANGE_BODY_OF_MESSAGE_TICKET, null, null);
        }

        if ($this->oldMessage->ticket_id != $this->newMessage->ticket_id) {
//            dispatch(new LogTicketChangeOfTicketMessage($this->newMessage , $this->authUser , $this->newMessage->ticket_id , $this->oldMessage->ticket_id));
            TicketActionLogRepo::new($this->newMessage->ticket_id, $this->newMessage->id, $this->authUser->id,
                TicketAction::CHANGE_TICKET_OF_TICKET_MESSAGE, $this->oldMessage->ticket_id,
                $this->newMessage->ticket_id);
        }

        if ($this->oldMessage->user_id != $this->newMessage->user_id) {
//            dispatch(new LogUserChangeOfTicketMessage($this->newMessage , $this->authUser , $this->newMessage->user_id , $this->oldMessage->user_id));
            TicketActionLogRepo::new($this->newMessage->ticket_id, $this->newMessage->id, $this->authUser->id,
                TicketAction::CHANGE_USER_OF_MESSAGE_TICKET, $this->oldMessage->user_id, $this->newMessage->user_id);
        }
    }
}
