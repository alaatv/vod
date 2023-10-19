<?php

namespace App\Jobs;

use App\Models\User;
use App\Traits\APIRequestCommon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Update3AUserInfo implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use APIRequestCommon;

    private User $user;

    /**
     * Create a new job instance.
     *
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        $params = [
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'province' => optional(optional($user->shahr)->ostan)->name,
            'city' => optional($user->shahr)->name,
            'major' => optional($user->major)->name,
            'grade' => optional($user->grade)->displayName,
            'gender' => optional($user->gender)->name,
            'school' => $user->school,
        ];

        $result = $this->update3AUserRequest($user, $params);

        if (!$result) {
            Log::error("user {$user->id} was not updated!");
        }
    }
}
