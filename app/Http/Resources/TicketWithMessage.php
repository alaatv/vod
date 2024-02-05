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
class TicketWithMessage extends AlaaJsonResource
{
    use Resource;
    use ResourceCommon;
    use TicketCommon;

    /** @var \App\Models\User */
    private $authUser;

    public function __construct(Ticket $model)
    {
        $this->authUser = auth()->check() ? auth()->user() : null;
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
        $this->authUser = auth()->user();

        $assignees = $this->assignees;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'user' => new TicketSender($this->user),
            'priority' => new TicketPriority($this->priority),
            'status' => new TicketStatus($this->status),
            'department' => new TicketDepartment($this->department),
            'orderproduct' => isset($this->orderproduct) ? new OrderProductInTicket($this->orderproduct) : null,
            'order' => isset($this->order) ? new OrderInTicket($this->order) : null,
            'messages' => TicketMessage::collection($this->getMessages()),
            'logs' => TicketLogInTicket::collection($this->getLogs()),
            'tags' => $this->when(isset($this->tags), function () {
                return $this->getTag();
            }),
            'ticket_form' => $this->form,
            'assignees' => $assignees->isNotEmpty() ? TicketSender::collection($assignees) : collect(),
            'rate' => $this->getRate(),
            'updated_at' => $this->getLasMessageCreationTime(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }

    private function getMessages()
    {
        $messages = $this->messages_orderby_time;
        if (! optional($this->authUser)->isAbleTo(config('constants.SHOW_TICKET_ACCESS'))) {
            $messages = $messages->where('is_private', 0);
        }

        return $messages;
    }
}
