<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class LogoutAllUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:LogoutAllUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'signs out all users from their accounts';

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
        $users = User::all();
        $usersCount = $users->count();
        if (!$this->confirm("$usersCount users found , Do you want to continue logging out these users", true)) {
            return 0;
        }
        $bar = $this->output->createProgressBar($usersCount);
        foreach ($users as $user) {
            auth()->loginUsingId($user->id);
            auth()->logoutOtherDevices($user->nationalCode);


            //Removing redis sessions:
            $redis = Redis::connection('session');
            //get all session IDs for user
            $userSessions = $redis->smembers('users:sessions:'.$user->id);
            $currentSession = Session::getId();
            //for logout from all devices use loop
            foreach ($userSessions as $sessionId) {
                if ($currentSession == $sessionId) {
                    continue;
                }
                //for remove sessions ID from array of user sessions (if user logout or manually logout )
                $redis->srem('users:sessions:'.$user->id, $sessionId);
                //remove Laravel session (logout user from other device)
                $redis->unlink('laravel:'.$sessionId);

            }
            auth()->logout();
            // Get remember_me cookie name
            $rememberMeCookie = Auth::getRecallerName();
            // Tell Laravel to forget this cookie
            $cookie = Cookie::forget($rememberMeCookie);

            $bar->advance();
        }

        $bar->finish();

    }
}
