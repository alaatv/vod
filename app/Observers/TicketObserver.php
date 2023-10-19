<?php

namespace App\Observers;

use App\Classes\Search\Tag\TaggingInterface;
use App\Jobs\LogRemovingTicket;
use App\Models\Ticket;
use App\Traits\TaggableTrait;
use Illuminate\Support\Facades\Cache;

class TicketObserver
{
    private $tagging;

    use TaggableTrait;

    public function __construct(TaggingInterface $tagging)
    {
        $this->tagging = $tagging;
    }

    /**
     * Handle the ticket "created" event.
     *
     * @param  Ticket  $ticket
     *
     * @return void
     */
    public function created(Ticket $ticket)
    {
    }

    /**
     * Handle the ticket "updated" event.
     *
     * @param  Ticket  $ticket
     * @return void
     */
    public function updated(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the ticket "deleted" event.
     *
     * @param  Ticket  $ticket
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        dispatch(new LogRemovingTicket($ticket->id, auth()->id()));
        Cache::tags(['ticket_'.$ticket->id, 'ticket_search'])->flush();
    }

    /**
     * Handle the ticket "restored" event.
     *
     * @param  Ticket  $ticket
     * @return void
     */
    public function restored(Ticket $ticket)
    {
        //
    }

    /**
     * Handle the ticket "force deleted" event.
     *
     * @param  Ticket  $ticket
     * @return void
     */
    public function forceDeleted(Ticket $ticket)
    {
        //
    }

    public function saved(Ticket $ticket)
    {
        $this->sendTagsOfTaggableToApi($ticket, $this->tagging);
        Cache::tags(['ticket_'.$ticket->id, 'ticket_search'])->flush();
    }
}
