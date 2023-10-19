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

class DeleteFromSearchia implements ShouldQueue
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

        try {
            $this->deleteFromSearchia($hitId);
        } catch (Exception $exception) {
            Log::channel('debug')->error(get_class($this->model)." by id={$this->model->id}, not deleted from searchia");
        }
    }

    private function deleteFromSearchia($hitId)
    {
        $query = self::MAIN_URL.$hitId;
        $response = Http::withHeaders(SearchiaSearch::HEADERS)
            ->delete($query)
            ->body();

        $status = json_decode($response)?->statusType;
        if ($status !== 'SUCCESS') {
            Log::channel('debug')->error("Fail deleting Document id=$hitId.($status)");
        }
    }
}
