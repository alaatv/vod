<?php

namespace App\Providers;

use App\Models\Draft;
use App\Models\FavorableList;
use App\Models\StudyEventReport;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\DraftPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App' => 'App\Policies',
        Draft::class => DraftPolicy::class,
        Ticket::class => TicketPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //TODO:// Extend Auth so that we can use alaatv guard in a middleware
        Auth::viaRequest('alaatv', function (Request $request) {
//            return $request->user('api');
            return User::find(1);
        });

        Gate::define('askPermission', function (User $user) {
            return $user->id == request('user_id');
        });

        Gate::define('viewWebSocketsDashboard', function ($user = null) {
            return $user?->isAn('admin');
        });

        Gate::define('read-study-event-report', function (User $user, StudyEventReport $studyEventReport) {
            return $user->id === $studyEventReport->user_id;
        });

        Gate::define('show-update-delete-favorable-list', function (User $user, FavorableList $favorableList) {
            return $user->id === $favorableList->user_id;
        });
    }
}
