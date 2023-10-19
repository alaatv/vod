<?php

namespace App\Models;


class TicketPriority extends BaseModel
{
    protected $table = 'ticketPriorities';

    protected $fillable = [
        'title',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::Class, 'priority_id', 'id');
    }

}
