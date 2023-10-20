<?php


namespace App\Repositories;


use App\Models\TicketActionLog;

class TicketActionLogRepo
{
    /**
     *
     *
     * @param  int  $ticketId
     * @param  int|null  $ticketMessageId
     * @param  int|null  $userId
     * @param  int  $actionId
     * @param  string|null  $before
     * @param  string|null  $after
     *
     * @return TicketActionLog
     */
    public static function new(
        int $ticketId,
        ?int $ticketMessageId,
        ?int $userId,
        int $actionId,
        ?string $before = null,
        ?string $after = null
    ): TicketActionLog {
        return TicketActionLog::create([
            'ticket_id' => $ticketId,
            'ticket_message_id' => $ticketMessageId,
            'user_id' => $userId,
            'action_id' => $actionId,
            'before' => $before,
            'after' => $after
        ]);
    }
}
