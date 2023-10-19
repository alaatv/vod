<?php

namespace App\Jobs;

use App\Classes\Uploader\Uploader;
use App\Exports\DefaultClassExport;
use App\Models\User;
use App\Notifications\NotifReportFailure;
use App\Notifications\SendReportLink;
use App\Traits\SearchiaCommonTrait;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportGroupRegistrationExcel implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SearchiaCommonTrait;
    use SerializesModels;

    private User $user;
    private $rows;
    private array $headers;

    public function __construct(User $user, $rows)
    {
        $this->user = $user;
        $this->headers = $rows['0']->toArray();
        unset($rows['0']);
        $this->rows = $rows;
    }

    public function handle()
    {
        $disk = config('disks.GROUP_REGISTRATION_REPORT_MINIO');
        $fileName = 'report_'.(Carbon::now('Asia/Tehran')->timestamp).'.xlsx';
        $diskPath = config("filesystems.disks.{$disk}.path");

        if (Excel::store(new DefaultClassExport($this->rows, $this->headers), $diskPath.$fileName, $disk)) {
            $link = Uploader::url($disk, $fileName);
            $this->user->notify(new SendReportLink($link));
        } else {
            $this->user->notify(new NotifReportFailure());
        }
    }
}
