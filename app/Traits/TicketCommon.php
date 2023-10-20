<?php

namespace App\Traits;


use App\Models\TicketAction;
use Illuminate\Support\Collection;

trait TicketCommon
{
    private function getLogs(): Collection
    {
        $logs = $this->logs_orderby_time;
        if (!optional($this->authUser)->isAbleTo(config('constants.SHOW_TICKET_ACCESS'))) {
            $logs = $logs->where('action_id', '!=', TicketAction::CREATE_TICKET_PRIVATE_MESSAGE);
        }

        return $logs;
    }

}
