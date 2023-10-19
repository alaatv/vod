<?php

namespace App\Models;


class TicketAction extends BaseModel
{
    public const CREATE_TICKET = 1;
    public const UPDATE_TICKET = 2;
    public const REMOVE_TICKET = 3;
    public const CHANGE_TITLE_OF_TICKET = 4;
    public const CHANGE_STATUS_OF_TICKET = 5;
    public const CHANGE_DEPARTMENT_OF_TICKET = 6;
    public const CHANGE_PRIORITY_OF_TICKET = 7;
    public const CHANGE_USER_OF_TICKET = 8;
    public const CHANGE_ORDERPRODUCT_OF_TICKET = 9;
    public const CREATE_TICKET_MESSAGE = 10;
    public const CREATE_TICKET_PRIVATE_MESSAGE = 11;
    public const UPDATE_TICKET_MESSAGE = 12;
    public const REMOVE_TICKET_MESSAGE = 13;
    public const CHANGE_BODY_OF_MESSAGE_TICKET = 14;
    public const CHANGE_TICKET_OF_TICKET_MESSAGE = 15;
    public const CHANGE_USER_OF_MESSAGE_TICKET = 16;
    public const CHANGE_PHOTO_OF_MESSAGE_TICKET = 17;
    public const CHANGE_VOICE_OF_MESSAGE_TICKET = 18;

    protected $table = 'ticketActions';

    protected $fillable = [
        'title',
    ];

    public function logs()
    {
        return $this->hasMany(TicketActionLog::Class, 'action_id', 'id');
    }
}
