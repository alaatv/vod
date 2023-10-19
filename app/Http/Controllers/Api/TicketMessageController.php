<?php

namespace App\Http\Controllers\Api;

use App\Classes\TicketChangeLogger;
use App\Classes\TicketMessageChangeLogger;
use App\Classes\Uploader\Uploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditTicketMessageRequest;
use App\Http\Requests\InsertTicketMessageRequest;
use App\Http\Requests\ListTicketMessageRequest;
use App\Http\Resources\TicketAndMessage;
use App\Http\Resources\TicketMessage as TicketMessageResource;
use App\Models\TicketMessage;
use App\Models\TicketStatus;
use App\Repositories\TicketMessageRepo;
use App\Traits\CharacterCommon;
use App\Traits\FileCommon;
use App\Traits\RequestCommon;
use App\Traits\Ticket\TicketHelper;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class TicketMessageController extends Controller
{
    use RequestCommon;
    use FileCommon;
    use TicketHelper;
    use CharacterCommon;

    /**
     * TicketMessageController constructor.
     */
    public function __construct()
    {
        $authException = $this->getAuthExceptionArray();
        $this->callMiddlewares($authException);
    }

    /**
     *
     * @return array
     */
    private function getAuthExceptionArray(): array
    {
        return [];
    }

    /**
     * @param $authException
     */
    private function callMiddlewares(array $authException): void
    {
        $this->middleware('auth', ['except' => $authException]);
        $this->middleware('permission:'.config('constants.EDIT_TICKET_ACCESS'), ['only' => 'update']);
        $this->middleware('permission:'.config('constants.REMOVE_TICKET_ACCESS'), ['only' => 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  ListTicketMessageRequest  $request
     *
     * @return JsonResponse
     */
    public function index(ListTicketMessageRequest $request)
    {
        $ticketMessages = TicketMessage::query()->orderBy('created_at', 'desc');
        return TicketMessageResource::collection($ticketMessages)->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InsertTicketMessageRequest  $request
     *
     * @return JsonResponse
     */
    public function store(InsertTicketMessageRequest $request)
    {
        $authUser = $request->user();
        $ticketUserId = $request->user_id; // Note: $request->get('user_id') not working and does not return user id
        $isMessagePrivate = (bool) $request->get('is_private', false);
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
        try {
            $ticketMessage = TicketMessageRepo::new($request->get('ticket_id'), $ticketUserId, $request->get('body'),
                $filesArray, $isMessagePrivate);
        } catch (QueryException $exception) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }
        $ticket = $ticketMessage->ticket;
//        dispatch(new LogInsertingTicketMessage($ticketMessage, $authUser->id));
        $this->logTicketMessageInsertion($ticketMessage, $authUser->id);

        if (!$isMessagePrivate) {
            if ($authUser->isAbleTo(config('constants.INSERT_TICKET_ACCESS'))) {
                if (!$ticket->isAnswered()) {
                    $ticketStatusId = TicketStatus::STATUS_ANSWERED;
                }
            } else {
                if (!$ticket->isUnAnswered()) {
                    $ticketStatusId = TicketStatus::STATUS_UNANSWERED;
                }
            }
        }

        if (!isset($ticketStatusId)) {

            $data = [
                'ticket' => $ticket,
                'ticketMessage' => $ticketMessage
            ];
            return (new TicketAndMessage($data))->response();
        }

        try {
            $oldTicket = $ticket->replicate();
            $ticket->update(['status_id' => $ticketStatusId]);
        } catch (QueryException $exception) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }

        $ticket = $ticket->fresh();
        (new TicketChangeLogger($oldTicket, $ticket, $authUser))->log();


        $data = [
            'ticket' => $ticket,
            'ticketMessage' => $ticketMessage
        ];
        return (new TicketAndMessage($data))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditTicketMessageRequest  $request
     * @param  TicketMessage  $ticketMessage
     *
     * @return JsonResponse
     */
    public function update(EditTicketMessageRequest $request, TicketMessage $ticketMessage)
    {
        $authUser = $request->user();
        $files = $ticketMessage->files;
        $oldPhoto = optional($files)->photos[0];
        $oldVoice = optional($files)->voices[0];
        $oldTicketMessage = $ticketMessage->replicate();

        if ($this->requestHasNull($request, 'photo')) {
            $photo = null;
        } else {
            $photo = Uploader::put($request->file('photo'), config('disks.TICKET_PHOTO_MINIO'));
            if (is_null($photo)) {
                $photo = optional($oldPhoto)->url;
            }
        }

        if ($this->requestHasNull($request, 'voice')) {
            $voice = null;
        } else {
            $voice = Uploader::put($request->file('voice'), config('disks.TICKET_VOICE_MINIO'));
            if (is_null($voice)) {
                $voice = optional($oldVoice)->url;
            }
        }

        $ticketMessage->files = $this->makeTicketMessageFilesArray($photo, $voice);

        try {
            $updateResult = $ticketMessage->update([
                'ticket_id' => $request->get('ticket_id', $ticketMessage->ticket_id),
                'user_id' => $request->get('user_id', $ticketMessage->user_id),
                'body' => $request->get('body', $ticketMessage->body),
            ]);
        } catch (QueryException $exception) {
            return myAbort(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getMessage());
        }

        if ($updateResult) {
            (new TicketMessageChangeLogger($oldTicketMessage, $ticketMessage, $authUser))->log();

            return (new TicketMessageResource($ticketMessage))->response();
        }

        return response()->json(['message' => 'خطای پایگاه داده']);
    }

    /**
     * Display the specified resource.
     *
     * @param  Request  $request
     * @param  TicketMessage  $ticketMessage
     *
     * @return JsonResponse
     */
    public function show(Request $request, TicketMessage $ticketMessage)
    {
        $user = $request->user();
        if (!$user->isAbleTo(config('constants.SHOW_TICKET_ACCESS')) && $ticketMessage->user_id != $user->id) {
            return myAbort(Response::HTTP_FORBIDDEN, 'شما اجازه مشاهده این تیکت را ندارید');
        }

        return (new TicketMessageResource($ticketMessage))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TicketMessage  $ticketMessage
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(TicketMessage $ticketMessage)
    {
        if ($ticketMessage->delete()) {
            Cache::tags([
                'ticket_'.$ticketMessage->ticket_id, 'ticketMessage_'.$ticketMessage->id,
                'ticket_'.$ticketMessage->ticket_id, 'ticket_search'
            ])->flush();
            return response()->json(['message' => 'پیام با موفقیت حذف شد']);
        }

        return response()->json(['خطای پایگاه داده'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function report(Request $request, TicketMessage $ticketMessage)
    {
        if (optional($ticketMessage)->ticket->user_id != $request->user()->id) {
            return response()->json(['message' => 'شما گزارش دادن این پیام را ندارید'], Response::HTTP_UNAUTHORIZED);
        }

        $ticketMessage->has_reported = 1;
        $description = $request->get('report_description');
        if (!$this->strIsEmpty($description)) {
            $ticketMessage->report_description = $description;
        }

        try {
            $ticketMessage->update();
            Cache::tags(['ticketMessage_'.$ticketMessage->id])->flush();
        } catch (QueryException $e) {
            return response()->json(['message' => 'خطا در پایگاه داده'], Response::HTTP_SERVICE_UNAVAILABLE);
        }


        return response()->json(['message' => 'تیکت با موفقیت گزارش شد']);

    }
}
