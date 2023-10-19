<?php

namespace App\Http\Resources;

use App\Models\TicketActionLog;
use App\Models\TicketActionLog;
use Illuminate\Http\Request;


/**
 * Class \App\TicketActionLog
 *
 * @mixin TicketActionLog
 * */
class TicketLogInTicket extends AlaaJsonResource
{
    /**
     * @var User $user
     */
    private $user;

    public function __construct(TicketActionLog $model)
    {
        parent::__construct($model);
        $this->user = $model->user;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user' => new TicketSender($this->getLogger()),
            'action' => optional($this->action)->title,
            'before' => $this->before,
            'after' => $this->after,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }

    private function getLogger(): ?\App\User
    {
        if (is_null($this->user_id)) {
            return null;
        }

        return $this->user;
    }
}
