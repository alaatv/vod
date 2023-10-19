<?php

namespace App\Console\Commands;

use App\Events\ChangeDanaStatus;
use App\Models\Content;
use App\Models\DanaContentTransfer;

use App\Models\DanaProductContentTransfer;

use App\Models\DanaProductSetTransfer;

use App\Models\DanaProductTransfer;

use App\Services\DanaProductService;
use App\Services\DanaService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SyncCommandFromAlaaToDana extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fromAlaaToDana:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'syncing From Alaa To Dana Command';

    private $activate = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $time = Carbon::now()->subHours(24);
        $productIds =
            DanaProductTransfer::where('status',
                DanaProductTransfer::SUCCESSFULLY_TRANSFERRED)->pluck('product_id')->toArray();
        $contents = Content::whereRelation('set.products', function ($query) use ($productIds) {
            return $query->whereIn('id', $productIds);
        })->where('updated_at', '>=', $time->toDateTimeString())->with([
            'set.products' => function ($query) use ($productIds) {
                return $query->whereIn('id', $productIds);
            },
        ])->withTrashed()->get();

        Log::channel('danaSync')->error("in SyncCommandFromAlaaToDana : start processing contents : total items : {$contents->count()}");
        foreach ($contents as $content) {
            foreach ($content->set->products as $product) {
                try {
                    $danaProduct = DanaProductTransfer::where('product_id', $product->id)->first();
                    if ($danaProduct->insert_type == 1) {
                        $this->checkContentTableTypeOne($danaProduct->dana_course_id, $content);
                        $this->checkForActivateCourseInDana($danaProduct->dana_course_id);
                    } else {
                        if ($danaProduct->insert_type == 2) {
                            $this->checkContentTableTypeTwo($danaProduct->dana_course_id, $content, $product->id);
                            $this->checkForActivateCourseInDana($danaProduct->dana_course_id);
                        }
                    }
                } catch (Exception $exception) {
                    Log::channel('danaSync')->error("in SyncCommandFromAlaaToDana : error on processing product {$product->id} of content {$content->id} : {$exception->getMessage()} at {$exception->getLine()}");
                }

            }
        }
    }

    private function checkContentTableTypeOne($courseId, $content)
    {
        $danaContentInfo = DanaContentTransfer::where('dana_course_id', $courseId)->where('educationalcontent_id',
            $content->id)->first();
        if (!$content->enable or !is_null($content->deleted_at)) {
            if (is_null($danaContentInfo)) {
                return $this->checkDeletedInDanaTypeOne($content, $courseId);
            }

            $response = $this->checkDeletedInDanaTypeOne($content, $courseId, $danaContentInfo->dana_session_id);
            if ($response and !is_null($danaContentInfo)) {
                $danaContentInfo->delete();
            }
        } else {
            $contentUpdatedAt = Carbon::parse($content->updated_at);
            if (is_null($danaContentInfo)) {
                return $this->checkExistForDeleteAndCreateDanaContentTypeOne($content, $courseId);
            }

            $danaContentInfoUpdatedAt = Carbon::parse($danaContentInfo->updated_at);

            if ($danaContentInfoUpdatedAt->lt($contentUpdatedAt->subMinutes(10)) or $danaContentInfoUpdatedAt->gt($contentUpdatedAt->addMinutes(10))) {
                $danaContentInfo->delete();
                return $this->checkExistForDeleteAndCreateDanaContentTypeOne($content, $courseId,
                    $danaContentInfo->dana_session_id);
            }
            return $this->checkExistForDeleteAndCreateDanaContentTypeOne($content, $courseId,
                $danaContentInfo->dana_session_id, 'check', $danaContentInfo);

        }

    }

    private function checkDeletedInDanaTypeOne($content, $courseId, $sessionId = null)
    {
        if ($sessionId == null) {
            $sessionName = $content->order == 0 ? $content->name : $content->displayName;
            $sessionId = DanaService::getDanaSessionId($courseId, $sessionName);
        }

        if (is_null($sessionId)) {
            Log::channel('danaSync')->error("in fromAlaaToDanaSync session id not found for courseId={$courseId} and contentId={$content->id}");
            return null;
        }

        $response = DanaService::getDanaSessionContent($courseId, $sessionId);
        $name = $content->contenttype_id == 8 ? 'فیلم' : 'جزوه';
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['name'] == $name) {
                $deleteResponse = DanaProductService::deleteContent($row['courseId'], $row['session'],
                    $row['contentId'], $row['type']);
                $this->activate = true;
                if (($deleteResponse['status_code'] == 200 && $deleteResponse['isSuccess'])) {
                    return true;
                } else {
                    Log::channel('danaSync')->error("can not delete dana content with id={$content->id} in fromAlaaToDanaSync : ".$deleteResponse['status_code']);
                    return null;
                }
            }
        }


    }

    private function checkExistForDeleteAndCreateDanaContentTypeOne(
        $content,
        $courseId,
        $sessionId = null,
        $type = null,
        $danaContentInfo = null
    ) {
        if ($sessionId == null) {
            $sessionName = $content->order == 0 ? $content->name : $content->displayName;
            $sessionId = DanaService::getDanaSessionId($courseId, $sessionName);
        }
        if (is_null($sessionId)) {
            Log::channel('danaSync')->error("in fromAlaaToDanaSync session id not found for courseId={$courseId} and contentId={$content->id}");
            return null;
        }

        $response = DanaService::getDanaSessionContent($courseId, $sessionId);
        $name = $content->contenttype_id == 8 ? 'فیلم' : 'جزوه';
        $danaContent = [];
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['name'] == $name) {
                $danaContent = $row;
            }
        }
        if (is_null($type)) {
            if (!empty($danaContent)) {
                $deleteResponse = DanaProductService::deleteContent($danaContent['courseId'], $danaContent['session'],
                    $danaContent['contentId'], $danaContent['type']);
                $this->activate = true;
                if (($deleteResponse['status_code'] != 200 or !$deleteResponse['isSuccess'])) {
                    Log::channel('danaSync')->error("can not delete dana content with id={$content->id} in fromAlaaToDanaSync : ".$deleteResponse['status_code']);
                    return null;
                }
            }
            $response = DanaService::createContent($courseId, $sessionId, $content);
            $this->activate = true;
            if (!$response) {
                Log::channel('danaSync')->error("in fromAlaaToDanaSync : checkExistOrCreateDanaContentTypeOne : can not create content for courseId={$courseId} and sessionId={$sessionId} and contentId={$content->id}");
            }

        } else {
            if (empty($danaContent)) {
                $danaContentInfo->delete();
                $response = DanaService::createContent($courseId, $sessionId, $content);
                $this->activate = true;
                if (!$response) {
                    Log::channel('danaSync')->error("in fromAlaaToDanaSync : checkExistOrCreateDanaContentTypeOne : can not create content for courseId={$courseId} and sessionId={$sessionId} and contentId={$content->id}");
                }
            }
        }

    }

    private function checkForActivateCourseInDana($courseId)
    {
        if ($this->activate) {
            ChangeDanaStatus::dispatch($courseId);
            $this->activate = false;
        }
    }

    private function checkContentTableTypeTwo($courseId, $content, $productId)
    {
        $danaContentInfo = DanaProductContentTransfer::where('dana_course_id',
            $courseId)->where('educationalcontent_id', $content->id)->first();
        if (is_null($danaContentInfo)) {
            $sessionId = DanaProductSetTransfer::where('contentset_id', $content->contentset_id)->where('product_id',
                $productId)->first()?->dana_session_id;
        } else {
            $sessionId = $danaContentInfo->dana_session_id;
        }
        if (!$content->enable or !is_null($content->deleted_at)) {
            $response = $this->checkDeletedInDanaTypeTwo($content, $courseId, $sessionId);
            if ($response and !is_null($danaContentInfo)) {
                $danaContentInfo->delete();
            }
        } else {
            $contentUpdatedAt = Carbon::parse($content->updated_at);
            if (is_null($danaContentInfo)) {
                return $this->checkExistForDeleteAndCreateDanaContentTypeTwo($content, $courseId, $sessionId);
            }

            $danaContentInfoUpdatedAt = Carbon::parse($danaContentInfo->updated_at);

            if ($danaContentInfoUpdatedAt->lt($contentUpdatedAt->subMinutes(10)) or $danaContentInfoUpdatedAt->gt($contentUpdatedAt->addMinutes(10))) {
                $danaContentInfo->delete();
                return $this->checkExistForDeleteAndCreateDanaContentTypeTwo($content, $courseId, $sessionId);
            }

            return $this->checkExistForDeleteAndCreateDanaContentTypeTwo($content, $courseId, $sessionId, 'check',
                $danaContentInfo);
        }

    }

    private function checkDeletedInDanaTypeTwo($content, $courseId, $sessionId = null)
    {
        if ($sessionId == null) {
            $sessionName = $content->set->small_name;
            $sessionId = DanaService::getDanaSessionId($courseId, $sessionName);
        }

        if (is_null($sessionId)) {
            Log::channel('danaSync')->error("in fromAlaaToDanaSync session id not found for courseId={$courseId} and contentId={$content->id}");
            return null;
        }

        $response = DanaService::getDanaSessionContent($courseId, $sessionId);
        $contentTitle = explode('-', $content->name);
        $contentTitle = Arr::get($contentTitle, 2);
        if (!isset($contentTitle)) {
            $contentTitle = $content->name;
        }
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['name'] == $contentTitle) {
                $deleteResponse = DanaProductService::deleteContent($row['courseId'], $row['session'],
                    $row['contentId'], $row['type']);
                $this->activate = true;
                if (($deleteResponse['status_code'] == 200 && $deleteResponse['isSuccess'])) {
                    return true;
                } else {
                    Log::channel('danaSync')->error("can not delete dana content with id={$content->id} in fromAlaaToDanaSync : ".$deleteResponse['status_code']);
                    return null;
                }
            }
        }
    }

    private function checkExistForDeleteAndCreateDanaContentTypeTwo(
        $content,
        $courseId,
        $sessionId = null,
        $type = null,
        $danaContentInfo = null
    ) {
        if ($sessionId == null) {
            $sessionName = $content->set->small_name;
            $sessionId = DanaService::getDanaSessionId($courseId, $sessionName);
        }

        if (is_null($sessionId)) {
            Log::channel('danaSync')->error("in fromAlaaToDanaSync session id not found for courseId={$courseId} and contentId={$content->id}");
            return null;
        }

        $response = DanaService::getDanaSessionContent($courseId, $sessionId);
        $contentTitle = explode('-', $content->name);
        $contentTitle = Arr::get($contentTitle, 2);
        if (!isset($contentTitle)) {
            $contentTitle = $content->name;
        }
        $danaContent = [];
        foreach ($response as $key => $row) {
            if (gettype($key) != 'integer') {
                continue;
            }

            if ($row['name'] == $contentTitle) {
                $danaContent = $row;
            }
        }
        if (is_null($type)) {
            if (!empty($danaContent)) {
                $deleteResponse = DanaProductService::deleteContent($danaContent['courseId'], $danaContent['session'],
                    $danaContent['contentId'], $danaContent['type']);
                $this->activate = true;
                if (($deleteResponse['status_code'] != 200 or !$deleteResponse['isSuccess'])) {
                    Log::channel('danaSync')->error("can not delete dana content with id={$content->id} in fromAlaaToDanaSync : ".$deleteResponse['status_code']);
                    return null;
                }
            }
            $response = DanaProductService::createContent($content, $courseId, $sessionId);
            $this->activate = true;
            if (!$response) {
                Log::channel('danaSync')->error("in fromAlaaToDanaSync : checkExistOrCreateDanaContentTypeOne : can not create content for courseId={$courseId} and sessionId={$sessionId} and contentId={$content->id}");
            }

        } else {
            if (empty($danaContent)) {
                $danaContentInfo->delete();
                $response = DanaProductService::createContent($content, $courseId, $sessionId);
                $this->activate = true;
                if (!$response) {
                    Log::channel('danaSync')->error("in fromAlaaToDanaSync : checkExistOrCreateDanaContentTypeOne : can not create content for courseId={$courseId} and sessionId={$sessionId} and contentId={$content->id}");
                }
            }
        }

    }
}
