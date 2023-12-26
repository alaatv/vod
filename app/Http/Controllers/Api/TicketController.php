<?php

namespace App\Http\Controllers\Api;

use App\Classes\Search\TicketSearch;
use App\Classes\TicketChangeLogger;
use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditTicketRequest;
use App\Http\Requests\InsertTicketRequest;
use App\Http\Requests\ListTicketRequest;
use App\Http\Resources\TicketDepartment as TicketDepartmentResource;
use App\Http\Resources\TicketPriority as TicketPriorityResource;
use App\Http\Resources\TicketWithMessage;
use App\Http\Resources\TicketWithoutMessage;
use App\Models\Ticket;
use App\Models\TicketDepartment;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\User;
use App\Notifications\TicketStatusChanged;
use App\Repositories\TicketMessageRepo;
use App\Repositories\TicketRepo;
use App\Traits\FileCommon;
use App\Traits\RequestCommon;
use App\Traits\Ticket\TicketHelper;
use App\Traits\UserCommon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends Controller
{
    use FileCommon;
    use RequestCommon;
    use TicketHelper;
    use UserCommon;

    /**
     * TicketMessageController constructor.
     */
    public function __construct()
    {
        $authException = $this->getAuthExceptionArray();
        $this->callMiddlewares($authException);
    }

    private function getAuthExceptionArray(): array
    {
        return [];
    }

    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
        $this->middleware('permission:'.config('constants.EDIT_TICKET_ACCESS'), ['only' => 'update']);
        $this->middleware('permission:'.config('constants.REMOVE_TICKET_ACCESS'), ['only' => 'destroy']);
        $this->middleware('permission:'.config('constants.SEND_TICKET_STATUS_NOTICE'),
            ['only' => 'sendTicketStatusChangeNotice']);
        $this->middleware('permission:'.config('constants.ASSIGN_TICKET'), ['only' => 'assignToUser']);
        //        $this->middleware('permission:' . config('constants.CREATE_TICKET'), ['only' => 'create']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ListTicketRequest $request, TicketSearch $ticketSearch): JsonResponse
    {
        $ticketDepartments = $request->get('department_id');
        if (isset($ticketDepartments)) {
            $childrenDepartments = TicketDepartment::query()->whereIn('parent_id',
                $ticketDepartments)->get()->pluck('id')->toArray();
            $ticketDepartments = array_merge($ticketDepartments, $childrenDepartments);

            $request->offsetSet('department_id', $ticketDepartments);
        }

        $tickets = $ticketSearch->get($request->all());

        $productId = $request->get('product_id');
        if (isset($productId)) {
            $tickets = Ticket::query()->whereIn('id', $tickets->pluck('id')->toArray())->whereHas('orderproduct',
                function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                })->get();
        }

        return TicketWithoutMessage::collection($tickets)->response();
    }

    public function create(): JsonResponse
    {
        $departments = TicketRepo::getRootDepartmentsSortedByOrder()->enable()->display()->get();
        $priorities = TicketPriority::all();

        return response()->json([
            'departments' => TicketDepartmentResource::collection($departments),
            'priorities' => TicketPriorityResource::collection($priorities),
            'statuses' => \App\Http\Resources\TicketStatus::collection(TicketStatus::all()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     *
     * @throws AuthorizationException
     */
    public function store(InsertTicketRequest $request): JsonResponse
    {
        $this->authorize('create', [Ticket::class, $request->get('department_id')]);
        $ticketUserId = $request->user_id;
        $authUser = $request->user();
        $isMessagePrivate = (bool) $request->get('is_private', false);
        $request->offsetSet('tags', convertTagStringToArray($request->get('tags')));
        try {
            $ticket = TicketRepo::new($ticketUserId, $request->get('title'), TicketStatus::DEFAULT_STATUS,
                $request->get('department_id'), $request->get('priority_id'), $request->get('orderproduct_id'),
                $request->get('tags'), $request->get('order_id'), $authUser->id, $request->get('related_entity_id'));
        } catch (QueryException $exception) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
        $this->logTicketInsertion($ticket->id, $authUser->id);

        try {
            if ($photo = $request->file('photo') ?? null) {
                $photo = Uploader::put($request->file('photo'), config('disks.TICKET_PHOTO_MINIO'));
            }
            if ($voice = $request->file('voice') ?? null) {
                $voice = Uploader::put($request->file('voice'), config('disks.TICKET_VOICE_MINIO'));
            }
            if ($file = $request->file('file') ?? null) {
                $file = Uploader::put($request->file('file'), config('disks.TICKET_FILE_MINIO'));
            }
            $filesArray = $this->makeTicketMessageFilesArray($photo, $voice, $file);

            $ticketMessage = TicketMessageRepo::new($ticket->id, $ticketUserId, $request->get('body'), $filesArray,
                $isMessagePrivate);
        } catch (QueryException $exception) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
        $this->logTicketMessageInsertion($ticketMessage, $authUser->id);

        return (new TicketWithMessage($ticket))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Ticket $ticket): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        if (! $user->isAbleTo(config('constants.SHOW_TICKET_ACCESS')) && $ticket->user_id != $user->id) {
            return myAbort(Response::HTTP_FORBIDDEN, 'شما اجازه مشاهده این تیکت را ندارید');
        }

        if ($user->isAbleTo(config('constants.SHOW_TICKET_ACCESS')) && isset($ticket->user)) {
            $otherThickets = $ticket->user->tickets
                ->where('department_id', $ticket->department_id)
                ->where('id', '<>', $ticket->id)
                ->sortByDesc('created_at');
        }

        return response()->json(
            [
                'ticket' => new TicketWithMessage($ticket),
                'other_tickets' => isset($otherThickets) ? TicketWithoutMessage::collection($otherThickets) : null,
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        if ($ticket->delete()) {
            Cache::tags(['ticket_'.$ticket->id, 'ticket_search'])->flush();

            return response()->json(['message' => 'تیکت با موفقیت حذف شد']);
        }

        return response()->json(['خطای پایگاه داده'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function sendTicketStatusChangeNotice(Ticket $ticket): JsonResponse
    {
        $user = $ticket->user;
        if (! isset($user)) {
            return response()->json(['message' => 'کاربر مالک تیکت یافت نشد'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->notify(new TicketStatusChanged(optional($ticket->status)->title, $ticket->id));

        return response()->json(['message' => 'اطلاع رسانی با موفقیت ارسال شد']);
    }

    /**
     * @throws ValidationException
     */
    public function assign(Request $request, Ticket $ticket): JsonResponse
    {
        Validator::make($request->all(), [
            'assignees' => ['array'],
        ])->validate();

        $ticket->assignees()->sync($request->get('assignees'));
        Cache::tags(['ticket_search'])->flush();

        return response()->json(['message' => 'تیکت با موفقیت به مسئولان اختصاص یافت']);
    }

    /**
     * @throws ValidationException
     */
    public function rate(Request $request, Ticket $ticket): JsonResponse
    {
        if ($ticket->user_id != $request->user()->id) {
            return response()->json(['message' => 'شما اجازه امتیازدهی به این تیکت را ندارید'],
                Response::HTTP_UNAUTHORIZED);
        }

        Validator::make($request->all(), [
            'rate' => ['required', 'integer', 'min:1'],
        ])->validate();

        $ticket->rate = $request->get('rate');
        try {
            $ticket->update();
            Cache::tags(['ticket_'.$ticket->id])->flush();
        } catch (QueryException) {
            return response()->json(['message' => 'خطا در پایگاه داده'], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return response()->json(['message' => 'تیکت با موفقیت امتیازدهی شد']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $authUser = $request->user();
        $oldTicket = $ticket->replicate();
        try {
            $updateResult = $ticket->update([
                'title' => $request->get('title', $ticket->title),
                'user_id' => $request->get('user_id', $ticket->user_id),
                'department_id' => $request->get('department_id', $ticket->department_id),
                'priority_id' => $request->get('priority_id', $ticket->priority_id),
                'status_id' => $request->get('status_id', $ticket->status_id),
                'orderproduct_id' => $request->get('orderproduct_id', $ticket->orderproduct_id),
                'rate' => $request->get('rate', $ticket->rate),
                'tags' => convertTagStringToArray($request->get('tags', convertArrayToTagString($ticket->tags?->tags))),
            ]);
        } catch (QueryException $exception) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }

        if (! $updateResult) {
            return response()->json(['message' => 'خطای پایگاه داده']);
        }

        $ticket = $ticket->fresh();
        (new TicketChangeLogger($oldTicket, $ticket, $authUser))->log();

        return (new TicketWithoutMessage($ticket))->response();
    }
}
