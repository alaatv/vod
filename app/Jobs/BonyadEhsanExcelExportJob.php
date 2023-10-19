<?php

namespace App\Jobs;

use App\Classes\Uploader\Uploader;
use App\Exports\BonyadUsersExport;
use App\Models\BonyadEhsanExcelExport;
use App\Models\BonyadEhsanExcelExport;
use App\Models\User;
use App\Services\BonyadService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BonyadEhsanExcelExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $queue;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $request;
    private $tableId;
    private $action = '';
    private $authUser;

    public function __construct($request, $tableId, $authUser)
    {
        $this->queue = 'default3';
        $this->request = $request;
        $this->tableId = $tableId;
        $this->authUser = $authUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $search = [];
        if (isset($this->request['first_name'])) {
            $search['filterFirstName'] = $this->request['first_name'];
        }
        if (isset($this->request['last_name'])) {
            $search['filterLastName'] = $this->request['last_name'];
        }
        if (isset($this->request['mobile'])) {
            $search['filterMobile'] = $this->request['mobile'];
        }
        if (isset($this->request['national_code'])) {
            $search['filterNationalCode'] = $this->request['national_code'];
        }

        if (isset($this->request['action'])) {
            $this->action = $this->request['action'];
            $bonyadUsers = BonyadService::users($this->authUser->id, $this->request['action'], false, $search);
        } else {
            /** @var User $user */
            if (isset($this->request['user_id'])) {
                $user = User::find($this->request['user_id']);
                $this->action = 'export sub level of user with id='.$this->request['user_id'];
            } else {
                $user = $this->authUser;
                $this->action = 'export sub level of own';
            }
            $bonyadUsers = BonyadService::userLevel($user, false, $search);
        }
        BonyadEhsanExcelExport::where('id', $this->tableId)->update(['total_user' => $bonyadUsers->count()]);

        try {
            $fileName = 'users_'.time().'.xls';
            Excel::store(
                new BonyadUsersExport($bonyadUsers),
                config('filesystems.disks.'.config('disks.EXCEL_REPORT').'.path').$fileName,
                config('disks.EXCEL_REPORT')
            );
            BonyadEhsanExcelExport::where('id', $this->tableId)->update([
                'export_link' => Uploader::url(config('disks.EXCEL_REPORT'), $fileName, false),
                'action' => $this->action,
                'status' => true,
            ]);
        } catch (Exception $exception) {
            BonyadEhsanExcelExport::where('id', $this->tableId)->update([
                'action' => $this->action,
                'status' => false,
            ]);
            Log::error('in ExamResultController : uploading export file to MinIO :'.$exception->getFile().' : line '.$exception->getLine().' : '.$exception->getMessage());
        }
    }
}
