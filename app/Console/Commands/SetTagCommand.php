<?php

namespace App\Console\Commands;

use App\Classes\Search\Tag\TaggingInterface;
use App\Models\Contentset;
use App\Traits\TaggableTrait;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SetTagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:seed:tag:set {set : The ID of the set}';

    use TaggableTrait;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'adds tags for a set';

    private $tagging;

    /**
     * SetTagCommand constructor.
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
        $setId = (int) $this->argument('set');
        if ($setId > 0) {
            try {
                $set = Contentset::findOrFail($setId);
            } catch (ModelNotFoundException $exception) {
                $this->error($exception->getMessage());

                return null;
            }
            if ($this->confirm('You have chosen\n\r '.$set->name.'. \n\rDo you wish to continue?', true)) {
                $this->performTaggingTaskForASet($set);
            }
        } else {
            $this->performTaggingTaskForAllSets();
        }
    }

    private function performTaggingTaskForASet(Contentset $set)
    {
        $this->sendTagsOfTaggableToApi($set, $this->tagging);
    }

    private function performTaggingTaskForAllSets(): void
    {
        $sets = Contentset::all();
        $setCount = $sets->count();
        if (!$this->confirm("$setCount sets found. Do you wish to continue?", true)) {
            $this->info('DONE!');
            return;
        }
        $bar = $this->output->createProgressBar($setCount);
        foreach ($sets as $set) {
            $this->performTaggingTaskForASet($set);
            $bar->advance();
        }
        $bar->finish();

        $this->info('DONE!');
    }
}
