<?php

namespace App\Console\Commands;

use App\Classes\Search\Tag\TaggingInterface;
use App\Models\Content;
use App\Traits\TaggableTrait;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContentTagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:seed:tag:content {content : The ID of the content}';

    use TaggableTrait;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'adds tags for a content';

    private $tagging;

    /**
     * ContentTagCommand constructor.
     *
     * @param  TaggingInterface  $tagging
     */
    public function __construct(TaggingInterface $tagging)
    {
        parent::__construct();
        $this->tagging = $tagging;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $contentId = (int) $this->argument('content');
        if ($contentId > 0) {
            try {
                $content = Content::findOrFail($contentId);
            } catch (ModelNotFoundException $exception) {
                $this->error($exception->getMessage());

                return null;
            }
            if ($this->confirm('You have chosen\n\r '.$content->display_name.'. \n\rDo you wish to continue?', true)) {
                $this->performTaggingTaskForAContent($content);
            }
        } else {
            $this->performTaggingTaskForAllContents();
        }
    }

    private function performTaggingTaskForAContent(Content $content)
    {
        $this->sendTagsOfTaggableToApi($content, $this->tagging);
    }

    private function performTaggingTaskForAllContents(): void
    {
        $contents = Content::all();
        $contentCount = $contents->count();
        if (!$this->confirm("$contentCount contents found. Do you wish to continue?", true)) {
            $this->info('DONE!');
            return;
        }
        $bar = $this->output->createProgressBar($contentCount);
        foreach ($contents as $content) {
            $this->performTaggingTaskForAContent($content);
            $bar->advance();
        }
        $bar->finish();

        $this->info('DONE!');
    }
}
