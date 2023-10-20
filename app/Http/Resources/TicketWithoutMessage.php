<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use App\Traits\ResourceCommon;
use App\Traits\Ticket\Resource;
use App\Traits\TicketCommon;
use Illuminate\Http\Request;


/**
 * Class \App\Ticket
 *
 * @mixin Ticket
 * */
class TicketWithoutMessage extends AlaaJsonResource
{
    use ResourceCommon;
    use Resource;
    use TicketCommon;

    /** @var \App\User $authUser */
    private $authUser;

    public function __construct(Ticket $model)
    {
        $this->authUser = auth()->user();
        parent::__construct($model);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'user' => new TicketSender($this->user),
            'priority' => new TicketPriority($this->priority),
            'status' => new TicketStatus($this->status),
            'department' => new TicketDepartment($this->department),
            'orderproduct' => $this->orderproduct_id,
            'logs' => TicketLogInTicket::collection($this->getLogs()),
            'tags' => $this->when(isset($this->tags), function () {
                return $this->getTag();
            }),
            'rate' => $this->getRate(),
            'updated_at' => $this->getLasMessageCreationTime(),
            'created_at' => $this->created_at->toDateTimeString(),
            // TODO: The following code doesn't work.
//            'last_responder' => $this->when(isset($this->last_ticket_responder), new TicketSender($this->last_ticket_responder)),
            'last_responder' => isset($this->last_ticket_responder) ? new TicketSender($this->last_ticket_responder) : null,
        ];
    }
}
