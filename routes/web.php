<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HackathonController as AdminHackathonController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Judge\ScoreController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Organizer\AnnouncementController;
use App\Http\Controllers\Organizer\HackathonController;
use App\Http\Controllers\Organizer\HackathonStatusController;
use App\Http\Controllers\Organizer\JudgeAssignmentController;
use App\Http\Controllers\Organizer\ScoringCriteriaController;
use App\Http\Controllers\Organizer\SegmentController;
use App\Http\Controllers\Participant\ParticipantAnnouncementController;
use App\Http\Controllers\Participant\SubmissionController;
use App\Http\Controllers\Participant\SubmissionFileController;
use App\Http\Controllers\Participant\TeamController;
use App\Http\Controllers\Participant\TeamInviteController;
use App\Http\Controllers\Participant\TeamMemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// ── Public Routes ────────────────────────────────────────────────
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/hackathons', [PublicController::class, 'index'])->name('hackathons.index');
Route::get('/h/{hackathon}', [PublicController::class, 'show'])->name('hackathons.show');

// ── Dashboard ────────────────────────────────────────────────────
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Notifications ────────────────────────────────────────────
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])
        ->name('notifications.markAllRead');
});

// ── Organizer Routes ─────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:organizer|super_admin'])
    ->prefix('organizer')
    ->name('organizer.')
    ->group(function () {
        Route::resource('hackathons', HackathonController::class);

        Route::post('hackathons/{hackathon}/status', HackathonStatusController::class)
            ->name('hackathons.status');

        Route::resource('hackathons.segments', SegmentController::class)
            ->only(['store', 'update', 'destroy']);

        Route::post('hackathons/{hackathon}/organizers', [HackathonController::class, 'inviteOrganizer'])
            ->name('hackathons.organizers.invite');

        Route::delete('hackathons/{hackathon}/organizers/{user}', [HackathonController::class, 'removeOrganizer'])
            ->name('hackathons.organizers.remove');

        // Organizer — re-open a finalized submission
        Route::post('submissions/{submission}/reopen', [SubmissionController::class, 'reopen'])
            ->name('submissions.reopen');

        // ── Scoring Criteria ─────────────────────────────────────────
        Route::get('hackathons/{hackathon}/criteria', [ScoringCriteriaController::class, 'index'])
            ->name('hackathons.criteria.index');
        Route::post('hackathons/{hackathon}/criteria', [ScoringCriteriaController::class, 'store'])
            ->name('hackathons.criteria.store');
        Route::put('hackathons/{hackathon}/criteria/{criterion}', [ScoringCriteriaController::class, 'update'])
            ->name('hackathons.criteria.update');
        Route::delete('hackathons/{hackathon}/criteria/{criterion}', [ScoringCriteriaController::class, 'destroy'])
            ->name('hackathons.criteria.destroy');

        // ── Judge Assignments ────────────────────────────────────────
        Route::get('hackathons/{hackathon}/judges', [JudgeAssignmentController::class, 'index'])
            ->name('hackathons.judges.index');
        Route::post('hackathons/{hackathon}/judges', [JudgeAssignmentController::class, 'store'])
            ->name('hackathons.judges.store');
        Route::delete('hackathons/{hackathon}/judges/{judge}', [JudgeAssignmentController::class, 'destroy'])
            ->name('hackathons.judges.destroy');

        // ── Announcements ────────────────────────────────────────────
        Route::resource('hackathons.announcements', AnnouncementController::class)
            ->except(['show']);
    });

// ── Judge Routes ─────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:judge|super_admin'])
    ->prefix('judging')
    ->name('judging.')
    ->group(function () {
        Route::get('/', [ScoreController::class, 'dashboard'])->name('dashboard');
        Route::get('submissions/{submission}', [ScoreController::class, 'show'])->name('score');
        Route::post('submissions/{submission}', [ScoreController::class, 'store'])->name('score.store');
    });

// ── Participant / Team Routes ────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:participant|super_admin'])
    ->group(function () {
        Route::get('teams', [TeamController::class, 'index'])->name('teams.index');
        Route::get('hackathons/{hackathon}/teams/create', [TeamController::class, 'create'])->name('teams.create');
        Route::post('hackathons/{hackathon}/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::get('teams/{team}', [TeamController::class, 'show'])->name('teams.show');
        Route::put('teams/{team}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');

        Route::get('teams/join/{invite_code}', [TeamInviteController::class, 'show'])->name('teams.join');
        Route::post('teams/join/{invite_code}', [TeamInviteController::class, 'store'])->name('teams.join.accept');

        Route::delete('teams/{team}/members/{member}', [TeamMemberController::class, 'destroy'])->name('teams.members.destroy');

        // ── Submissions ──────────────────────────────────────────────
        Route::get('hackathons/{hackathon}/submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('hackathons/{hackathon}/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
        Route::get('submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
        Route::put('submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
        Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit'])->name('submissions.submit');

        // ── Submission Files ─────────────────────────────────────────
        Route::post('submissions/{submission}/files', [SubmissionFileController::class, 'store'])->name('submissions.files.store');
        Route::delete('submission-files/{submissionFile}', [SubmissionFileController::class, 'destroy'])->name('submissions.files.destroy');

        // ── Participant Announcements ────────────────────────────────
        Route::get('hackathons/{hackathon}/announcements', [ParticipantAnnouncementController::class, 'index'])
            ->name('participant.announcements.index');
        Route::get('hackathons/{hackathon}/announcements/{announcement}', [ParticipantAnnouncementController::class, 'show'])
            ->name('participant.announcements.show');
    });

// ── Leaderboard (any authenticated user) ─────────────────────────
Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('hackathons/{hackathon}/leaderboard', [LeaderboardController::class, 'show'])
            ->name('leaderboard.show');
    });

// ── Admin Routes ─────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        // Users
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');

        // Hackathons
        Route::get('hackathons', [AdminHackathonController::class, 'index'])->name('hackathons.index');
        Route::delete('hackathons/{hackathon}/force', [AdminHackathonController::class, 'forceDelete'])->name('hackathons.force-delete');
        Route::post('hackathons/{hackathon}/restore', [AdminHackathonController::class, 'restore'])->name('hackathons.restore');

        // Settings
        Route::get('settings', [SystemController::class, 'show'])->name('settings');
        Route::post('settings/general', [SystemController::class, 'updateGeneral'])->name('settings.general');
        Route::post('settings/registration', [SystemController::class, 'updateRegistration'])->name('settings.registration');
        Route::post('settings/uploads', [SystemController::class, 'updateUploads'])->name('settings.uploads');
    });

// ── Impersonation (any authenticated user can stop) ──────────────
Route::middleware(['auth'])
    ->post('admin/stop-impersonation', [UserController::class, 'stopImpersonation'])
    ->name('admin.stop-impersonation');

require __DIR__.'/auth.php';
