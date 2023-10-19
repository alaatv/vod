<?php

namespace App\Observers;

use App\Jobs\LogRemovingTicketMessage;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Cache;

class TicketMessageObserver
{
    /**
     * Handle the ticket message "created" event.
     *
     * @param  TicketMessage  $ticketMessage
     *
     * @return void
     */
    public function created(TicketMessage $ticketMessage)
    {
        //
    }

    /**
     * Handle the ticket message "updated" event.
     *
     * @param  TicketMessage  $ticketMessage
     *
     * @return void
     */
    public function updated(TicketMessage $ticketMessage)
    {
        //
    }

    /**
     * Handle the ticket message "deleted" event.
     *
     * @param  TicketMessage  $ticketMessage
     *
     * @return void
     */
    public function deleted(TicketMessage $ticketMessage)
    {
        dispatch(new LogRemovingTicketMessage($ticketMessage, auth()->id()));
        Cache::tags([
            'ticket_'.$ticketMessage->ticket_id, 'ticketMessage_'.$ticketMessage->id, 'ticket_search'
        ])->flush();
    }

    /**
     * Handle the ticket message "restored" event.
     *
     * @param  TicketMessage  $ticketMessage
     *
     * @return void
     */
    public function restored(TicketMessage $ticketMessage)
    {
        //
    }

    /**
     * Handle the ticket message "force deleted" event.
     *
     * @param  TicketMessage  $ticketMessage
     *
     * @return void
     */
    public function forceDeleted(TicketMessage $ticketMessage)
    {
        //
    }

    public function saved(TicketMessage $ticketMessage)
    {
        Cache::tags([
            'ticket_'.$ticketMessage->ticket_id, 'ticketMessage_'.$ticketMessage->id, 'ticket_search'
        ])->flush();
    }
}
