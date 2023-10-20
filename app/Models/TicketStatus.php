<?php

namespace App\Models;


class TicketStatus extends BaseModel
{
    public const STATUS_UNANSWERED = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_ANSWERED = 3;
    public const STATUS_CLOSED = 4;
    public const DEFAULT_STATUS = self::STATUS_UNANSWERED;
    protected $table = 'ticketStatuses';
    protected $fillable = [
        'title',
        'name',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::Class, 'status+_id', 'id');
    }

}
