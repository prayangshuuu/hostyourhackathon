<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Enums\TeamRole;
use App\Models\Announcement;
use App\Models\Hackathon;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use App\Policies\HackathonPolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Hackathon::class => HackathonPolicy::class,
        Team::class => TeamPolicy::class,
        Submission::class => SubmissionPolicy::class,
        User::class => UserPolicy::class,
        Announcement::class => AnnouncementPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(fn (User $user, string $ability): ?bool => $user->hasRole(RoleEnum::SuperAdmin->value) ? true : null);

        Gate::define('teams.createOnHackathon', function (User $user, Hackathon $hackathon): bool {
            if (! $user->can('teams.create')) {
                return false;
            }

            if (! $hackathon->isRegistrationOpen()) {
                return false;
            }

            return ! $user->hasTeamInHackathon($hackathon);
        });

        Gate::define('submissions.createOnHackathon', function (User $user, Hackathon $hackathon): bool {
            if (! $user->can('submissions.create')) {
                return false;
            }

            $team = $user->teamInHackathon($hackathon);

            if (! $team) {
                return false;
            }

            $isLeader = $team->members()
                ->where('user_id', $user->id)
                ->where('role', TeamRole::Leader)
                ->exists();

            if (! $isLeader) {
                return false;
            }

            if (! $hackathon->isSubmissionOpen()) {
                return false;
            }

            $existing = Submission::where('team_id', $team->id)
                ->where('hackathon_id', $hackathon->id)
                ->first();

            if ($existing && $existing->isFinal()) {
                return false;
            }

            return true;
        });
    }
}
