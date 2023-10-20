<?php

namespace App\Models;

use App\Traits\GetTehranTimeZoneTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketActionLog extends BaseModel
{
    use HasFactory;
    use GetTehranTimeZoneTrait;

    protected $table = 'ticketActionLogs';

    protected $fillable = [
        'ticket_id',
        'ticket_message_id',
        'user_id',
        'action_id',
        'before',
        'after',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::Class);
    }

    public function action()
    {
        return $this->belongsTo(TicketAction::Class, 'action_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::Class);
    }
}
