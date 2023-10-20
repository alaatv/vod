<?php

namespace App\Console\Commands;

use App\Models\DanaContentTransfer;
use App\Models\DanaProductContentTransfer;
use App\Models\DanaProductSetTransfer;
use App\Models\DanaProductTransfer;
use App\Models\User;
use App\Notifications\BonyadConsultantCheckerNotification;
use App\Services\DanaProductService;
use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;

class CheckDanaSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dana:sync:check';

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
    private $notificationFlag = false;

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
        Log::channel('danaSync')->info('Running CheckDanaSyncCommand');
        $danaProducts = DanaProductTransfer::where('status', DanaProductTransfer::SUCCESSFULLY_TRANSFERRED)->get();

        $testCourseId = $danaProducts->first()->dana_course_id;
        $response = DanaProductService::getDanaSession($testCourseId);
        if ($response['status_code'] == Response::HTTP_UNAUTHORIZED) {
            Log::channel('danaSync')->error('In CheckDanaSyncCommand : Dana token has been expired');
            $this->notificationFlag = true;
            return false;
        }

        $count = $danaProducts->count();
        $progressBar = new ProgressBar($this->output, $count);
        $progressBar->start();

        foreach ($danaProducts as $danaProduct) {
            $sessions = $this->getCourseSessions($danaProduct->dana_course_id);
            foreach ($sessions as $session) {
                $contents = $this->getSessionContents($danaProduct->dana_course_id, $session['sessionID']);
                if ($danaProduct->insert_type == $this->foriat) {
                    $allContents = DanaContentTransfer::where('dana_course_id', $danaProduct->dana_course_id)
                        ->where('dana_session_id', $session['sessionID'])
                        ->where('status', DanaContentTransfer::SUCCESSFULLY_TRANSFERRED)
                        ->get();
                    if (count($contents) != $allContents->count()) {
                        \Log::channel('danaSync')->info("In CheckDanaSyncCommand : check foriat: count of contents does not match , courseId={$danaProduct->dana_course_id}, sessionId={$session['sessionID']}");
                        $this->notificationFlag = true;
                    }
                    foreach ($contents as $content) {
                        $danaContent =
                            DanaContentTransfer::where('dana_course_id',
                                $danaProduct->dana_course_id)->where('dana_session_id', $session['sessionID'])
                                ->where('dana_content_id', $content['id'])
                                ->where('dana_filemanager_content_id', $content['contentId'])
                                ->get();
                        if ($danaContent->isEmpty()) {
                            \Log::channel('danaSync')->info('In CheckDanaSyncCommand : check foriat: danaCourseId-'.$danaProduct->dana_course_id.' - danaSessionId-'.$session['sessionID'].' - danaContentId-'.$content['id'].' - danaFileManagerId'.$content['contentId'].'- not found');
                            $this->notificationFlag = true;
                            continue;
                        }
                        if ($danaContent->count() > 1) {
                            \Log::channel('danaSync')->info('In CheckDanaSyncCommand : check foriat: danaCourseId-'.$danaProduct->dana_course_id.' - danaSessionId-'.$session['sessionID'].' - danaContentId-'.$content['id'].' - danaFileManagerId'.$content['contentId'].'- more than one found');
                            $this->notificationFlag = true;
                            continue;
                        }
                    }
                } else {
                    if ($danaProduct->insert_type == $this->chatr) {
                        $danaSessions = DanaProductSetTransfer::where('dana_session_id', $session['sessionID'])
                            ->where('product_id', $danaProduct->product_id)->get();

                        if ($danaSessions->isEmpty()) {
                            \Log::channel('danaSync')->info('In CheckDanaSyncCommand : check chatr: danaCourseId-'.$danaProduct->dana_course_id.' - danaSessionId-'.$session['sessionID'].'- not found');
                            $this->notificationFlag = true;
                            continue;
                        }
                        if ($danaSessions->count() > 1) {
                            \Log::channel('danaSync')->info('In CheckDanaSyncCommand : check chatr: danaCourseId-'.$danaProduct->dana_course_id.' - danaSessionId-'.$session['sessionID'].'- more than one found');
                            $this->notificationFlag = true;
                            continue;
                        }
                        $allContents = DanaProductContentTransfer::where('dana_course_id', $danaProduct->dana_course_id)
                            ->where('dana_session_id', $session['sessionID'])
                            ->where('status', DanaProductContentTransfer::SUCCESSFULLY_TRANSFERRED)
                            ->get();
                        if (count($contents) != $allContents->count()) {
                            \Log::channel('danaSync')->info("In CheckDanaSyncCommand : check chatr: count of contents does not match , courseId={$danaProduct->dana_course_id}, sessionId={$session['sessionID']}");
                            $this->notificationFlag = true;
                        }
                        foreach ($contents as $content) {
                            $danaContent =
                                DanaProductContentTransfer::where('dana_course_id',
                                    $danaProduct->dana_course_id)->where('dana_session_id', $session['sessionID'])
                                    ->where('dana_content_id', $content['id'])
                                    ->where('dana_filemanager_content_id', $content['contentId'])
                                    ->get();
                            if ($danaContent->isEmpty()) {
                                \Log::channel('danaSync')->info('In CheckDanaSyncCommand : check chatr: danaCourseId-'.$danaProduct->dana_course_id.' - danaSessionId-'.$session['sessionID'].' - danaContentId-'.$content['id'].' - danaFileManagerId'.$content['contentId'].'- not found');
                                $this->notificationFlag = true;
                                continue;
                            }
                            if ($danaContent->count() > 1) {
                                \Log::channel('danaSync')->info('In CheckDanaSyncCommand : check: danaCourseId-'.$danaProduct->dana_course_id.' - danaSessionId-'.$session['sessionID'].' - danaContentId-'.$content['id'].' - danaFileManagerId'.$content['contentId'].'- more than one found');
                                $this->notificationFlag = true;
                                continue;
                            }
                        }
                    }
                }
            }
            $progressBar->advance();
        }

        if ($this->notificationFlag) {
            $user = User::find(1);
            $user->notify(new BonyadConsultantCheckerNotification('اختلافی در ظرفیت های ثبت نام بنیاد احسان'));
        }
        return true;
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
}
