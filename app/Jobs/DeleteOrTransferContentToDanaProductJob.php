<?php

namespace App\Jobs;

use App\Events\ChangeDanaStatus;
use App\Models\DanaProductContentTransfer;
use App\Models\DanaProductSetTransfer;
use App\Models\DanaProductTransfer;
use App\Services\DanaProductService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteOrTransferContentToDanaProductJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;
    private $content;
    private $renew;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($content, $renew = true)
    {
        $this->queue = 'default2';
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
        $contentset = $content->set;
        $danaProductContents = DanaProductContentTransfer::where('educationalcontent_id', $content->id)->get();

        if ($this->renew) {
            $transferredDanaProductContents =
                $danaProductContents->where('status', DanaProductContentTransfer::SUCCESSFULLY_TRANSFERRED);
            foreach ($transferredDanaProductContents as $danaProductContent) {
                try {
                    if ($content->contenttype_id == config('constants.CONTENT_TYPE_VIDEO')) {
                        $type = 1;
                    } else {
                        $type = 3;
                    }
                    if (isset($danaProductContent->dana_filemanager_content_id)) {
                        $deleteResponse = DanaProductService::deleteContent(
                            $danaProductContent->dana_course_id,
                            $danaProductContent->dana_session_id,
                            $danaProductContent->dana_filemanager_content_id,
                            $type
                        );
                    }

                    if (($deleteResponse['status_code'] == 200 && $deleteResponse['isSuccess'])) {
                        $danaProductContent->delete();
                    } else {
                        Log::channel('danaTransfer')->error("can not delete dana content with id={$danaProductContent->id} in dana_product_content_transfer table : ".$deleteResponse['status_code']);
                        return null;
                    }

                    if (isset($content->deleted_at) || !$content->enable) {
                        continue;
                    }

                    $createContent = DanaProductService::createContent(
                        $content,
                        $danaProductContent->dana_course_id,
                        $danaProductContent->dana_session_id,
                    );
                    ChangeDanaStatus::dispatch($danaProductContent->dana_course_id);

                } catch (Exception $exception) {
                    Log::channel('danaTransfer')->error(
                        "sessionId={$danaProductContent->dana_session_id},
                courseId={$danaProductContent->dana_course_id},
                contentId={$danaProductContent->dana_content_id},
                filemanagerId={$danaProductContent->dana_filemanager_content_id},
                educationalContentId={$danaProductContent->educationalcontent_id}
                has error with message={$exception->getMessage()} with statusCode={$exception->getCode()}
                ");
                }
            }
        }

        if (isset($content->deleted_at) || !$content->enable) {
            return null;
        }

        $danaProductSets =
            DanaProductSetTransfer::where('contentset_id', $contentset->id)->where('status',
                DanaProductSetTransfer::SUCCESSFULLY_TRANSFERRED)->get();
        foreach ($danaProductSets as $danaProductSet) {
            $danaCourseId =
                DanaProductTransfer::where('product_id', $danaProductSet->product_id)->first()?->dana_course_id;
            if (is_null($danaCourseId)) {
                continue;
            }
            if (DanaProductContentTransfer::where('educationalcontent_id', $content->id)->where('dana_course_id',
                $danaCourseId)->where('status', DanaProductContentTransfer::SUCCESSFULLY_TRANSFERRED)->exists()) {
                continue;
            }
            $createContent =
                DanaProductService::createContent($content, $danaCourseId, $danaProductSet->dana_session_id);
            if ($createContent) {
                ChangeDanaStatus::dispatch($danaCourseId);
            }
        }

        $danaProducts = $contentset->products;
        foreach ($danaProducts as $danaProduct) {
            $danaCourseId = DanaProductTransfer::where('product_id', $danaProduct->id)->first()?->dana_course_id;
            if (is_null($danaCourseId)) {
                continue;
            }
            if (DanaProductSetTransfer::where('product_id', $danaProduct->id)->where('contentset_id',
                $contentset->id)->where('status', DanaProductContentTransfer::SUCCESSFULLY_TRANSFERRED)->exists()) {
                continue;
            }

            $createSession =
                DanaProductService::createSession($danaCourseId, $contentset,
                    $contentset->getOriginal('pivot_order') == 0 ? 1 : $contentset->getOriginal('pivot_order'),
                    $danaProduct->id);
            if ($createSession) {
                ChangeDanaStatus::dispatch($danaCourseId);
            }
        }
        return null;
    }
}
