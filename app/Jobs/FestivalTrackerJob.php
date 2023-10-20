<?php

namespace App\Jobs;

use App\Models\TempFestivalVisits;
use App\Repositories\FestivalVisotorRepo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FestivalTrackerJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private string $mobile;

    public function __construct(string $mobile)
    {
        $this->mobile = $mobile;
    }

    public function handle()
    {
        $this->track($this->mobile);
    }

    private function track(string $mobile)
    {
        if ($visitor = FestivalVisotorRepo::findVisitorByMobile($mobile)) {
            $visitor->update(['visit_times' => $visitor->visit_times + 1]);
            return null;
        }

        TempFestivalVisits::query()->create(['mobile' => $mobile,]);
    }
}
