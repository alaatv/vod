<?php

namespace App\Jobs;

use App\Traits\GiveGift\GiveGift;
use App\Traits\GiveGift\GiveGiftPlans;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GiveGiftProductsJob implements ShouldQueue, GiveGift
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use GiveGiftPlans;

    public $queue;
    private $model;
    private $action;

    public function __construct(Model $model, string $action)
    {
        $this->queue = 'default2';
        $this->model = $model;
        $this->action = $action;
    }


    public function handle()
    {
        $this->{$this->action}($this->model, $this->action);
        return 0;
    }
}
