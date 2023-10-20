<?php

namespace App\Console\Commands;

use App\Jobs\InsertContentToSatra;
use App\Models\Content;
use Illuminate\Console\Command;

class InsertContentsToSatraCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:insert:contents:to:satra {id : The ID of the content to begin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sends contents to Satra';

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
     * @return mixed
     */
    public function handle()
    {
        $contentId = (int) $this->argument('id');
        if ($contentId > 0) {
            $contents = Content::query()
                ->where('id', '>=', $contentId)
                ->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))
                ->whereNull('redirectUrl')
                ->active()
                ->get();
        } else {
            $contents = Content::query()
                ->where('contenttype_id', config('constants.CONTENT_TYPE_VIDEO'))
                ->whereNull('redirectUrl')
                ->active()
                ->get();
        }

        $contentsCount = $contents->count();

        if (!$this->confirm('Found '.$contentsCount.' contents, Would you like to continue ?', true)) {
            return 0;
        }
        $bar = $this->output->createProgressBar($contentsCount);
        foreach ($contents as $content) {
            dispatch(new InsertContentToSatra($content));
            $bar->advance();
        }

        $bar->finish();
    }
}
