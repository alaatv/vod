<?php

namespace App\Jobs;

use App\Exports\SaleReportExport;
use App\Models\User;
use App\Notifications\NotifReportFailure;
use App\Notifications\SendReportLink;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportSaleReportExcel implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $timeout = 105;

    /**
     * @var User
     */
    private $user;

    /**
     * ExportSaleReportExcel constructor.
     *
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }


    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $fileName = 'report_'.(Carbon::now()->timestamp).'.xlsx';
        $disk = config('disks.GENERAL');

        if (Excel::store(new SaleReportExport(), $fileName, $disk)) {
            $link = route('web.download', ['content' => $disk, 'fileName' => $fileName]);
            $this->user->notify(new SendReportLink($link));
        }

        $this->user->notify(new NotifReportFailure());
    }
}
