<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConvertActivityLogCauserToUserInsertedByForBonyadUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaatv:activityLog:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'causedby to InsertedBy for bonyad users';

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
        $users = User::whereHas('roles', function ($query) {
            return $query->whereIn('id', [123, 124, 130, 131]);
        })->get();
        Log::channel('bonyadUserInsertedBy')->info('running activity log to user inserted by convertor for bonyad');
        foreach ($users as $user) {
            $activityLog = Activity::where('subject_id', $user->id)->where('log_name',
                config('activitylog.log_names.bonyad_user'))->latest()->first();
            if (!isset($activityLog)) {
                Log::channel('bonyadUserInsertedBy')->error("user with id={$user->id} does not have activity log");
                continue;
            }
            $causer = User::find($activityLog->causer_id);
            if (!isset($causer)) {
                Log::channel('bonyadUserInsertedBy')->warning("causer user with id={$activityLog->causer_id} does not exist,set inserted by to null for user {$user->id}");
                continue;
            }
            $user->update(['inserted_by' => $activityLog->causer_id]);
        }
        Log::channel('bonyadUserInsertedBy')->info('done');
        return 0;
    }
}
