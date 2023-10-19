<?php

namespace App\Jobs;

use App\Classes\Search\SearchStrategy\SearchiaSearch;
use App\Traits\SearchiaCommonTrait;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

//use App\Classes\TagsGroup;

class UpdateInSearchia implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SearchiaCommonTrait;
    use SerializesModels;

    private const MAIN_URL = SearchiaSearch::MAIN_URL
    .SearchiaSearch::INDEX
    .'/doc/';

    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        $hitId = $this->getFromSearchia();
        if (!$hitId) {
            Log::channel('debug')->error(get_class($this->model)." by id={$this->model->id}, not found in searchia");
            return null;
        }

        $this->updateInSearchia($hitId);
    }

    private function updateInSearchia($hitId)
    {
        $query = self::MAIN_URL.$hitId;
        try {
            Http::withHeaders(SearchiaSearch::HEADERS)
                ->put($query, $this->makeDocument());
        } catch (Exception $exception) {
            $message = get_class($this->model)
                ." by id={$this->model->id}, not updated in searchia.("
                .$exception->getMessage()
                .')';
            Log::channel('debug')->error($message);
        }
    }
}
