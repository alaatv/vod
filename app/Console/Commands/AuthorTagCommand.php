<?php

namespace App\Console\Commands;

use App\Classes\Search\Tag\TaggingInterface;
use App\Models\User;
use App\Traits\TaggableTrait;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthorTagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:seed:tag:author {author : The ID of the teacher}';

    use TaggableTrait;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds tags for an author';

    private $tagging;

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
        $authorId = (int) $this->argument('author');
        if ($authorId > 0) {
            try {
                $user = User::findOrFail($authorId);
            } catch (ModelNotFoundException $exception) {
                $this->error($exception->getMessage());

                return 0;
            }
            if ($this->confirm('You have chosen '.$user->full_name.'. Do you wish to continue?', true)) {
                $this->performTaggingTaskForAnAuthor($user);
            }
        } else {
            $this->performTaggingTaskForAllAuthors();
        }
    }

    /**
     * @param $user
     */
    private function performTaggingTaskForAnAuthor(User $user)
    {
        $userContents = $user->contents;
        if (count($userContents) == 0) {
            $this->error('user '.$user->id.' : '.$user->full_name.' has no content.');
            $this->info("\n");
        } else {
            $this->sendTagsOfTaggableToApi($user, $this->tagging);
        }
    }

    private function performTaggingTaskForAllAuthors(): void
    {
        $users = User::getTeachers();
        $teachersCount = $users->count();
        if (!$this->confirm("$teachersCount teachers found. Do you wish to continue?", true)) {
            $this->info('DONE');
            return;
        }
        $bar = $this->output->createProgressBar($teachersCount);
        foreach ($users as $user) {
            $this->performTaggingTaskForAnAuthor($user);
            $bar->advance();
        }
        $bar->finish();

        $this->info('DONE');
    }
}
