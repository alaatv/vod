<?php

namespace App\Models;

use App\Classes\Taggable;
use App\Traits\GetTehranTimeZoneTrait;
use App\Traits\Ticket\TaggableTicketTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Ticket extends BaseModel implements Taggable
{
    use HasFactory;
    use GetTehranTimeZoneTrait;
    use TaggableTicketTrait;

    public const PREVENT_REGISTER_NEW_TICKET_DATE = '2021-07-13';
    protected $fillable = [
        'title',
        'user_id',
        'insertor_id',
        'department_id',
        'priority_id',
        'status_id',
        'orderproduct_id',
        'order_id',
        'related_entity_id',
        'sms_notification',
        'tags',
    ];
    protected $cascadeDeletes = [
        'messages',
    ];

    public function user()
    {
        return $this->belongsTo(User::Class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::Class);
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::Class, 'status_id', 'id');
    }

    public function priority()
    {
        return $this->belongsTo(TicketPriority::Class, 'priority_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(TicketDepartment::Class, 'department_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany(TicketActionLog::Class);
    }

    public function orderproduct()
    {
        return $this->belongsTo(Orderproduct::Class);
    }

    public function order()
    {
        return $this->belongsTo(Order::Class);
    }

    /**
     * Set the content's tag.
     *
     * @param  array  $value
     *
     * @return void
     */
    public function setTagsAttribute(array $value = null)
    {
        $tags = null;
        if (!empty($value)) {
            $tags = json_encode([
                'bucket' => 'ticket',
                'tags' => $value,
            ], JSON_UNESCAPED_UNICODE);
        }

        $this->attributes['tags'] = $tags;
    }

    /**
     * Get the content's tags .
     *
     * @param $value
     *
     * @return mixed
     */
    public function getTagsAttribute($value)
    {
        return json_decode($value);
    }

    public function getMessagesOrderbyTimeAttribute()
    {
        $key = 'ticketMessagesOrderByTime:'.$this->cacheKey();
        return Cache::tags(['ticket', 'ticket_'.$this->id, 'ticket_'.$this->id.'_messages'])
            ->remember($key, config('constants.CACHE_10'), function () {
                return $this->messages()->orderByDesc('created_at')->get();
            });
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::Class);
    }

    public function getFormAttribute()
    {
        $department = $this->department;
        if (!isset($department)) {
            return null;
        }
        return $department->ticket_form;
    }

    public function getLastTicketResponderAttribute()
    {
        $lastTicketMessage = $this->messages()
            ->whereHas('user', function ($q) {
                $q->whereHas('roles', function ($q1) {
                    $q1->whereHas('permissions', function ($q2) {
                        $q2->where('name', config('constants.ANSWER_TICKET'));
                    });
                });
            })
            ->orderByDesc('created_at')
            ->first();
        return $lastTicketMessage ? $lastTicketMessage->user : null;
    }

    public function getLogsOrderbyTimeAttribute()
    {
        return $this->logs->sortByDesc('created_at');
    }

    public function isAnswered(): bool
    {
        return $this->status_id == TicketStatus::STATUS_ANSWERED;
    }

    public function isUnAnswered(): bool
    {
        return $this->status_id == TicketStatus::STATUS_UNANSWERED;
    }

    public function getVueJsSubDomain()
    {
        return '#/t/'.$this->id;
    }

    public function close()
    {
        $this->update([
            'status_id' => TicketStatus::STATUS_CLOSED,
        ]);
    }
}
