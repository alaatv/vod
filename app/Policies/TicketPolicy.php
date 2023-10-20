<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Ticket  $ticket
     * @return Response|bool
     */
    public function view(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user, int $departmentId)
    {
        $ticketLimit = TicketDepartment::find($departmentId)->ticket_limit_for_user;
        if ($ticketLimit == -1) {
            return true;
        }
        $userTickets = auth()->user()->tickets()->where('department_id', $departmentId)->get();
        $userTicketCounts = $userTickets->count();
        $userOldTicket = $userTickets->first();
        $message = 'لطفا در پیام خود را تیکتی که قبلا باز کرده اید ثبت کنید . ';
        if (isset($userOldTicket)) {
            $message =
                $message.'<a style="color:black; font-weight:bold ; text-decoration: underline;  " href="https://alaatv.com/t#/t/"'.$userOldTicket->id.'>کلیک کنید</a>';
        }
        return $userTicketCounts < $ticketLimit ? Response::allow() : Response::deny($message, 423);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Ticket  $ticket
     * @return Response|bool
     */
    public function update(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Ticket  $ticket
     * @return Response|bool
     */
    public function delete(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Ticket  $ticket
     * @return Response|bool
     */
    public function restore(User $user, Ticket $ticket)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Ticket  $ticket
     * @return Response|bool
     */
    public function forceDelete(User $user, Ticket $ticket)
    {
        //
    }
}
