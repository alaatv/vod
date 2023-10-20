<?php

namespace App\Jobs;

use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RemoveContentVideoFiles implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $content;

    /**
     * Create a new job instance.
     *
     * @param  Content  $content
     */
    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $CDNDisk = config('disks.ALAA_CDN_SFTP');
        foreach ($this->content->getVideos() as $video) {
            $path = explode('nodes.alaatv.com', $video->link)[1];
            if (Storage::disk($CDNDisk)->exists($path)) {
                Storage::disk($CDNDisk)->delete($path);
            }
        }
    }
}
