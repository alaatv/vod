<?php

namespace App\Listeners;

use App\Models\Order;
use App\Models\Product;
use App\Models\TicketStatus;
use App\Repositories\TicketMessageRepo;
use App\Repositories\TicketRepo;
use App\Traits\Ticket\TicketHelper;

class MakeTicketEntekhabReshteListener
{
    use TicketHelper;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->myOrder instanceof Order && $event->myOrder->orderproducts->whereIn('product_id',
                Product::ENTEKHAB_RESHTE_IDS)->count()) {


            $ticket = TicketRepo::new(
                $event->user->id,
                'درخواست انتخاب رشته',
                TicketStatus::DEFAULT_STATUS,
                25,
                4,
                null,
                null,
                $event->myOrder->id,
                $event->user->id,
                null
            );


            $filesArray = $this->makeTicketMessageFilesArray(null, null, null);


            TicketMessageRepo::new(
                $ticket->id,
                $event->user->id,
                "<a target='_blank' href='https://alaatv.com/admin/users/{$event->user->id}/event/13/entekhb-reshte'>لینک مشاهده اطلاعات انتخاب رشته</a>",
                $filesArray,
                true,
            );

            TicketMessageRepo::new(
                $ticket->id,
                $event->user->id,
                'مشاور در حال بررسی اطلاعات انتخاب رشته شما هستند. از شکیبایی شما متشکریم',
                $filesArray,
            );
        }
    }
}
