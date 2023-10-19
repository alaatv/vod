<?php

namespace App\Jobs;

use App\Models\User;
use App\Traits\UserCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveOldProfilePhoto implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UserCommon;

    protected $photoPath;
    protected $user;

    /**
     * RemoveOldProfilePhoto constructor.
     *
     * @param $photoPath
     */
    public function __construct($photoPath, User $user)
    {
        $this->photoPath = $photoPath;
        $this->user = $user;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->isDefaultProfilePhoto($this->photoPath)) {
            return null;
        }
        $this->user->deletePhoto(config('disks.PROFILE_IMAGE_MINIO'));
//        Storage::disk(config('disks.PROFILE_IMAGE_SFTP'))->delete($this->photoPath);
    }
}
