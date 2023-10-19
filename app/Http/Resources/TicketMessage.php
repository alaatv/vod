<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;


/**
 * Class \App\TicketMessage
 *
 * @mixin \App\TicketMessage
 * */
class TicketMessage extends AlaaJsonResource
{
    /** @var \App\User $authUser */
    private $authUser;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $this->authUser = auth()->user();

        return [
            'id' => $this->id,
            'user' => new TicketSender($this->user),
            'ticket_id' => $this->ticket_id,
            'body' => $this->body,
            'files' => ['photo' => $this->photo, 'voice' => $this->voice, 'file' => $this->file],
            'is_private' => $this->is_private,
            'report' => $this->getReportInfo(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }

    private function getReportInfo()
    {
        if (optional($this)->ticket->user_id == optional($this->authUser)->id) {
            return [
                'has_reported' => $this->has_reported,
                'report_description' => $this->report_description,
            ];
        }

        if (optional($this->authUser)->isAbleTo(config('constants.SHOW_TICKET_MESSAGE_REPORT_ACCESS'))) {
            return [
                'has_reported' => $this->has_reported,
                'report_description' => $this->report_description,
            ];
        }

        return [
            'has_reported' => 0,
            'report_description' => null,
        ];
    }
}
