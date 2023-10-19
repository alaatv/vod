<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Contentset;
use App\Models\DanaContentTransfer;
use App\Models\DanaProductContentTransfer;
use App\Models\DanaProductSetTransfer;
use App\Models\DanaProductTransfer;
use App\Services\DanaProductService;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;

class DanaSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dana:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fromDanaToALaaSync';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $foriat = 1;
    private $chatr = 2;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    //main handle
    public function handle()
    {
        Log::channel('danaSync')->info('Running DanaSyncCommand');
        $danaProducts = DanaProductTransfer::where('status', DanaProductTransfer::SUCCESSFULLY_TRANSFERRED)->get();
        $testCourseId = $danaProducts->first()->dana_course_id;
        $response = DanaProductService::getDanaSession($testCourseId);
        if ($response['status_code'] == Response::HTTP_UNAUTHORIZED) {
            Log::channel('danaSync')->error('In DanaSyncCommand : Dana token has been expired');
            return false;
        }

        $count = $danaProducts->count();
        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();

        foreach ($danaProducts as $danaProduct) {

            if ($danaProduct->insert_type == $this->foriat) {
                $this->syncTypeOne($danaProduct->dana_course_id);
            } else {
                if ($danaProduct->insert_type == $this->chatr) {
                    $this->syncTypeTwo($danaProduct->dana_course_id, $danaProduct->product_id);
                }
            }
            $progressBar->advance();
        }
        $progressBar->finish();
        $this->info("\n");
        $this->info('Done');
        return true;
    }

    private function syncTypeOne($courseId)
    {
        $sessions = $this->getCourseSessions($courseId);

        $existSessionIds = array_column($sessions, 'sessionID');
        DanaContentTransfer::whereNotIn('dana_session_id', $existSessionIds)->where('dana_course_id',
            $courseId)->delete();

        foreach ($sessions as $session) {
            $sessionContents = $this->getSessionContents($courseId, $session['sessionID']);
            $danaContents = DanaContentTransfer::where('dana_course_id', $courseId)
                ->where('dana_session_id', $session['sessionID'])
                ->get();

            $ourContentIds = $danaContents->pluck('id')->toArray();
            foreach ($sessionContents as $sessionContent) {

                $type = $sessionContent['type'] == 1 ? config('constants.CONTENT_TYPE_VIDEO') : config('constants.CONTENT_TYPE_PAMPHLET');

                if (strpos($sessionContent['url'], 'CpFolder') === false) {
                    $fileName = '/'.$sessionContent['url'];
                } else {
                    $fileName = substr($sessionContent['url'], strpos($sessionContent['url'], '/'));
                }
                $content = Content::whereJsonContains('file', [['fileName' => $fileName]])
                    ->where('contenttype_id', $type)
                    ->where('enable', 1)
                    ->get();

                if ($content->isEmpty()) {
                    \Log::channel('danaSync')->error("in dana:sync command : dana_content_transfers : content not find for courseId={$courseId} , sessionId={$session['sessionID']}
                        , dana_filemanager_id={$sessionContent['contentId']}");
                    continue;
                }
                if ($content->count() > 1) {
                    \Log::channel('danaSync')->error("in dana:sync command : dana_content_transfers :  find more than one content for courseId={$courseId} , sessionId={$session['sessionID']}
                        , dana_filemanager_id={$sessionContent['contentId']}");
                    continue;
                }
                $content = $content->first();


                $findFlag = false;
                foreach ($danaContents as $danaContent) {
                    if ($sessionContent['contentId'] == $danaContent->dana_filemanager_content_id and $sessionContent['id'] == $danaContent->dana_content_id and $findFlag == false) {
                        $findFlag = true;
                        if ($danaContent->educationalcontent_id != $content->id) {
                            $danaContent->update(['educationalcontent_id' => $content->id]);
                        }
                        $ourContentIdArrayKey = array_search($danaContent->id, $ourContentIds);
                        unset($ourContentIds[$ourContentIdArrayKey]);
                    }
                }

                if (!$findFlag) {
                    DanaContentTransfer::create([
                        'dana_course_id' => $courseId,
                        'dana_session_id' => $session['sessionID'],
                        'dana_content_id' => $sessionContent['id'],
                        'dana_filemanager_content_id' => $sessionContent['contentId'],
                        'educationalcontent_id' => $content->id,
                        'status' => DanaContentTransfer::SUCCESSFULLY_TRANSFERRED,
                    ]);
                }
            }
            DanaContentTransfer::whereIn('id', $ourContentIds)->delete();
        }
    }

    private function getCourseSessions($courseId)
    {
        $response = DanaProductService::getDanaSession($courseId);
        unset($response['status_code']);
        return $response;
    }

    private function getSessionContents($courseId, $sessionId)
    {
        $response = DanaProductService::getSessionContent($courseId, $sessionId);
        unset($response['status_code']);
        return $response;

    }

    private function syncTypeTwo($courseId, $productId)
    {
        $sessions = $this->getCourseSessions($courseId);
        $existSessionIds = array_column($sessions, 'sessionID');

        DanaProductContentTransfer::where('dana_course_id', $courseId)->whereNotIn('dana_session_id',
            $existSessionIds)->delete();
        DanaProductSetTransfer::where('product_id', $productId)->whereNotIn('dana_session_id',
            $existSessionIds)->delete();

        foreach ($sessions as $session) {
            $danaSet = DanaProductSetTransfer::where('dana_session_id', $session['sessionID'])->get();
            if ($danaSet->isEmpty()) {
                $contentSet = Contentset::where('small_name', $session['name'])->where('enable', 1)->get();
                if ($contentSet->isEmpty()) {
                    \Log::channel('danaSync')->error("in dana:sync command : dana_product_set_transfers : contentset not find for courseId={$courseId} , sessionId={$session['sessionID']}");
                    continue;
                }
                if ($contentSet->count() > 1) {
                    \Log::channel('danaSync')->error("in dana:sync command : dana_product_set_transfers :  find more than one contentset for courseId={$courseId} , sessionId={$session['sessionID']}");
                    continue;
                }
                $contentSet = $contentSet->first();
                DanaProductSetTransfer::create([
                    'dana_session_id' => $session['sessionID'],
                    'contentset_id' => $contentSet->id,
                    'status' => DanaProductSetTransfer::SUCCESSFULLY_TRANSFERRED,
                    'product_id' => $productId,
                ]);
            }


            $sessionContents = $this->getSessionContents($courseId, $session['sessionID']);

            $danaContents = DanaProductContentTransfer::where('dana_course_id', $courseId)
                ->where('dana_session_id', $session['sessionID'])
                ->get();

            $ourContentIds = $danaContents->pluck('id')->toArray();
            foreach ($sessionContents as $sessionContent) {
                $type = $sessionContent['type'] == 1 ? config('constants.CONTENT_TYPE_VIDEO') : config('constants.CONTENT_TYPE_PAMPHLET');

                if (strpos($sessionContent['url'], 'CpFolder') === false) {
                    $fileName = '/'.$sessionContent['url'];
                } else {
                    $fileName = substr($sessionContent['url'], strpos($sessionContent['url'], '/'));
                }
                $content = Content::whereJsonContains('file', [['fileName' => $fileName]])
                    ->where('contenttype_id', $type)
                    ->where('enable', 1)
                    ->get();

                if ($content->isEmpty()) {
                    \Log::channel('danaSync')->error("in dana:sync command : dana_product_content_transfers : content not find for courseId={$courseId} , sessionId={$session['sessionID']}
                        , dana_filemanager_id={$sessionContent['contentId']}");
                    continue;
                }
                if ($content->count() > 1) {
                    \Log::channel('danaSync')->error("in dana:sync command : dana_product_content_transfers :  find more than one content for courseId={$courseId} , sessionId={$session['sessionID']}
                        , dana_filemanager_id={$sessionContent['contentId']}");
                    continue;
                }
                $content = $content->first();


                $findFlag = false;
                foreach ($danaContents as $danaContent) {
                    if ($sessionContent['contentId'] == $danaContent->dana_filemanager_content_id and $sessionContent['id'] == $danaContent->dana_content_id and $findFlag == false) {
                        $findFlag = true;
                        if ($danaContent->educationalcontent_id != $content->id) {
                            $danaContent->update(['educationalcontent_id' => $content->id]);
                        }
                        $ourContentIdArrayKey = array_search($danaContent->id, $ourContentIds);
                        unset($ourContentIds[$ourContentIdArrayKey]);
                    }
                }

                if (!$findFlag) {
                    DanaProductContentTransfer::create([
                        'dana_course_id' => $courseId,
                        'dana_session_id' => $session['sessionID'],
                        'dana_content_id' => $sessionContent['id'],
                        'dana_filemanager_content_id' => $sessionContent['contentId'],
                        'educationalcontent_id' => $content->id,
                        'status' => DanaContentTransfer::SUCCESSFULLY_TRANSFERRED,
                    ]);
                }
            }
            DanaProductContentTransfer::whereIn('id', $ourContentIds)->delete();
        }
    }
}
