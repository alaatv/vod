<?php


namespace App\Traits\Ticket;


use App\Models\TicketAction;
use App\Repositories\TicketActionLogRepo;
use Illuminate\Support\Str;


trait TicketHelper
{
    private function makeTicketMessageFilesArray(?string $photo, ?string $voice, ?string $file = null): ?array
    {
        $files = [];

        if (isset($photo)) {
            $files['photos'] = [
                [
                    'uuid' => Str::uuid()->toString(),
                    'disk' => config('disks.TICKET_PHOTO_MINIO'),
                    'url' => $photo,
                    'fileName' => basename($photo),
                    'size' => null,
                    'ext' => pathinfo($photo, PATHINFO_EXTENSION),
                ]
            ];
        } else {
            $files['photos'] = null;
        }

        if (isset($voice)) {
            $files['voices'] = [
                [
                    'uuid' => Str::uuid()->toString(),
                    'disk' => config('disks.TICKET_VOICE_MINIO'),
                    'url' => $voice,
                    'fileName' => basename($voice),
                    'size' => null,
                    'ext' => pathinfo($voice, PATHINFO_EXTENSION),
                ]
            ];
        } else {
            $files['voices'] = null;
        }

        if (isset($file)) {
            $files['file'] = [
                [
                    'uuid' => Str::uuid()->toString(),
                    'disk' => config('disks.TICKET_FILE_MINIO'),
                    'url' => $file,
                    'fileName' => basename($file),
                    'size' => null,
                    'ext' => pathinfo($file, PATHINFO_EXTENSION),
                ]
            ];
        } else {
            $files['file'] = null;
        }
        return !empty($files) ? $files : null;
    }

    private function logTicketMessageInsertion(TicketMessage $ticketMessage, int $authUserId)
    {
        $action = TicketAction::CREATE_TICKET_MESSAGE;
        if ($ticketMessage->is_private) {
            $action = TicketAction::CREATE_TICKET_PRIVATE_MESSAGE;
        }
        TicketActionLogRepo::new($ticketMessage->ticket_id, $ticketMessage->id, $authUserId, $action);
    }

    private function logTicketInsertion(int $ticketId, int $authUserId)
    {
        TicketActionLogRepo::new($ticketId, null, $authUserId, TicketAction::CREATE_TICKET);
    }
}
