<?php

namespace App\Jobs;

use App\Collection\UserCollection;
use App\Notifications\DownloadAppNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SendDownloadAppNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $users;

    /**
     * Create a new job instance.
     *
     * @param  UserCollection  $users
     */
    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = $this->users;
        foreach ($users as $user) {
            $user->notify(new DownloadAppNotification());
        }
    }
}
