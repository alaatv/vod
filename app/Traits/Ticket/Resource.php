<?php


namespace App\Traits\Ticket;


trait Resource
{
    private function canSeeLogs()
    {
        return isset($this->logs_orderby_time) && optional($this->authUser)->isAbleTo(config('constants.SHOW_TICKET_LOGS_ACCESS'));
    }

    private function getLasMessageCreationTime()
    {
        $lastMessage = $this->messages->last();
        if (is_null($lastMessage)) {
            return null;
        }

        return $lastMessage->created_at->toDateTimeString();
    }

    private function getRate()
    {
        if ($this->user_id == optional($this->authUser)->id) {
            return $this->rate;
        }

        if (optional($this->authUser)->isAbleTo(config('constants.SHOW_TICKET_RATE_ACCESS'))) {
            return $this->rate;
        }

        return null;
    }
}
