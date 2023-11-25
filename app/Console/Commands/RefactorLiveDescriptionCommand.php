<?php

namespace App\Console\Commands;

use App\Models\LiveDescription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefactorLiveDescriptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:refactor:liveDescription {--rollback}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "in livedescriptions table if product_id is null make instance's entity_id = 5 and entity_type = App\Studyevent if product_id is not null make entity_id = product_id and entity_type = App\Models\Products";

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
     * @return int
     */
    public function handle()
    {
        Log::channel('debug')->debug('Running alaatv:refactor:liveDescription');

        if ($this->option('rollback')) {
            $this->fillProductId();
        } else {
            $this->fillEntity();
        }
    }

    private function fillProductId()
    {
        LiveDescription::withTrashed()->where('entity_type', '\App\Models\Product')->update([
            'product_id' => DB::raw('ENTITY_ID'),
        ]);
        LiveDescription::withTrashed()->where('entity_type', '\App\Studyevent')->update([
            'product_id' => null,
        ]);
    }

    private function fillEntity()
    {
        LiveDescription::withTrashed()->whereNull('product_id')->update([
            'entity_id' => 5,
            'entity_type' => 'App\Studyevent',
        ]);
        LiveDescription::withTrashed()->whereNotNull('product_id')->update([
            'entity_id' => DB::raw('PRODUCT_ID'),
            'entity_type' => 'App\Models\Product',
        ]);
    }
}
