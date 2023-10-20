<?php

namespace App\Jobs;

use App\Events\ChangeDanaStatus;
use App\Models\Content;
use App\Models\DanaContentTransfer;
use App\Models\DanaSetTransfer;
use App\Services\DanaProductService;
use App\Services\DanaService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DanaTransferJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;
    private $content;
    private $counter;
    private $renew;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($content, $renew = true)
    {
        $this->queue = 'default3';
        $this->content = $content;
        $this->renew = $renew;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $content = $this->content;
        $danaSet = DanaSetTransfer::where('contentset_id', $content->contentset_id)->get();
        if ($danaSet->isEmpty()) {
            return null;
        }
        $courseKey = $danaSet->first()->dana_course_id;
        $danaContents = DanaContentTransfer::where('educationalcontent_id', $content->id)->get();

        if ($this->renew) {
            $transferredDanaContents =
                $danaContents->where('status', DanaContentTransfer::SUCCESSFULLY_TRANSFERRED);
            foreach ($transferredDanaContents as $danaContent) {
                try {
                    if ($content->contenttype_id == config('constants.CONTENT_TYPE_VIDEO')) {
                        $type = 1;
                    } else {
                        $type = 3;
                    }
                    if (isset($danaContent->dana_filemanager_content_id)) {
                        $deleteResponse = DanaProductService::deleteContent(
                            $danaContent->dana_course_id,
                            $danaContent->dana_session_id,
                            $danaContent->dana_filemanager_content_id,
                            $type
                        );
                    }

                    if (($deleteResponse['status_code'] == 200 && $deleteResponse['isSuccess'])) {
                        $danaContent->delete();
                    } else {
                        Log::channel('danaTransfer')->error("can not delete dana content with id={$danaContent->id} in dana__content_transfer table : ".$deleteResponse['status_code']);
                        return null;
                    }

                    if (isset($content->deleted_at) || !$content->enable) {
                        continue;
                    }


                    $createContent =
                        DanaService::createContent($danaContent->dana_course_id, $danaContent->dana_session_id,
                            $content);
                    ChangeDanaStatus::dispatch($danaContent->dana_course_id);

                } catch (Exception $exception) {
                    Log::channel('danaTransfer')->error(
                        "dana_content_transfers table:
                sessionId={$danaContent->dana_session_id},
                courseId={$danaContent->dana_course_id},
                contentId={$danaContent->dana_content_id},
                filemanagerId={$danaContent->dana_filemanager_content_id},
                educationalContentId={$danaContent->educationalcontent_id}
                has error with message={$exception->getMessage()} with statusCode={$exception->getCode()}
                ");
                }
            }
        }

        if (isset($content->deleted_at) || !$content->enable) {
            return null;
        }


        if (DanaContentTransfer::where('educationalcontent_id', $content->id)->where('dana_course_id',
            $courseKey)->where('status', DanaContentTransfer::SUCCESSFULLY_TRANSFERRED)->exists()) {
            return null;
        }

        $order = $this->content->order;

        if ($this->content->contenttype_id == config('constants.CONTENT_TYPE_VIDEO')) {
            $introContent = Content::query()->where('contentset_id', $this->content->contentset_id)
                ->where('contenttype_id', $this->content->contenttype_id)
                ->where('order', 0)
                ->first();
            if (isset($introContent)) {
                $order = $this->content->order + 1;
            }
            $createSession = DanaService::createVideoSession($courseKey, $this->content, $order);
            ChangeDanaStatus::dispatch($courseKey);
        } else {
            if ($this->content->contenttype_id == config('constants.CONTENT_TYPE_PAMPHLET')
                && Content::active()->where('contentset_id', $this->content->contentset_id)
                    ->whereNull('redirectUrl')
                    ->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))
                    ->where('order', $this->content->order)
                    ->doesntExist()) {
                $createSession = DanaService::createPamphletSession($courseKey, $this->content, $order);
                ChangeDanaStatus::dispatch($courseKey);
            }
        }

        return null;
    }
}
