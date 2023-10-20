<?php


namespace App\Repositories;


use App\Models\TicketMessage;

class TicketMessageRepo
{
    /**
     *
     * @param  int  $ticketId
     * @param  int  $userId
     * @param  string|null  $body
     * @param  array|null  $filesArray
     *
     * @param  bool  $isPrivate
     *
     * @return TicketMessage
     */
    public static function new(
        int $ticketId,
        int $userId,
        ?string $body,
        ?array $filesArray,
        bool $isPrivate = false
    ): TicketMessage {
        return TicketMessage::create([
            'ticket_id' => $ticketId,
            'user_id' => $userId,
            'body' => $body,
            'files' => $filesArray,
            'is_private' => $isPrivate,
        ]);
    }
}
