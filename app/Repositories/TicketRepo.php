<?php


namespace App\Repositories;


use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class TicketRepo extends AlaaRepo
{
    public static function getModelClass(): string
    {
        return Ticket::class;
    }


    /**
     *
     * @param  int  $userId
     * @param  string|null  $title
     * @param  int|null  $statusId
     * @param  int|null  $departmentId
     * @param  int|null  $priorityId
     * @param  int|null  $orderproductId
     * @param  array|null  $tags
     *
     * @param  int|null  $orderId
     *
     * @param  int|null  $insertorId
     *
     * @param  int|null  $related_entity_id
     *
     * @return Ticket
     */
    public static function new(
        int $userId,
        ?string $title,
        ?int $statusId,
        ?int $departmentId,
        ?int $priorityId,
        ?int $orderproductId,
        ?array $tags,
        ?int $orderId,
        ?int $insertorId,
        ?int $related_entity_id
    ): Ticket {
        return Ticket::create([
            'user_id' => $userId,
            'insertor_id' => $insertorId,
            'title' => $title,
            'status_id' => $statusId,
            'department_id' => $departmentId,
            'priority_id' => $priorityId,
            'orderproduct_id' => $orderproductId,
            'order_id' => $orderId,
            'related_entity_id' => $related_entity_id,
            'tags' => $tags,
        ]);
    }

    public static function getRootDepartmentsSortedByOrder(): Builder
    {
        return TicketDepartment::query()->orderBy('order')->whereNull('parent_id');
    }

    /**
     * @param  int  $user_id
     * @param  int  $departmentId
     * @param  string|null  $startDate
     * @return mixed
     */
    public static function userSpecialDepartmentTicket(int $user_id, int $departmentId, string $startDate = null)
    {
        $ticket = Ticket::where('user_id', $user_id)
            ->where('department_id', $departmentId);

        if (!is_null($startDate)) {
            $ticket = $ticket->where('created_at', '>', $startDate);
        }

        return $ticket;
    }

    public static function getUnAnsweredTickets($filters = [], $query = null)
    {
        if (is_null($query)) {
            $query = self::initiateQuery();
        }

        $query = $query->where('status_id', TicketStatus::STATUS_UNANSWERED);

        if (Arr::has($filters, 'since')) {
            $query = self::createdSince(Arr::get($filters, 'since'), $query);
        }

        if (Arr::has($filters, 'till')) {
            $query = self::createdTill(Arr::get($filters, 'till'), $query);
        }

        if (Arr::has($filters, 'department_id')) {
            $query = $query->where('department_id', Arr::get($filters, 'department_id'));
        }

        return $query;
    }

}
