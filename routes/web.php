<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HackathonController as AdminHackathonController;
use App\Http\Controllers\Admin\SystemController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HackathonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Judge\ScoreController;
use App\Http\Controllers\JudgeDashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Organizer\AnnouncementController;
use App\Http\Controllers\Organizer\HackathonController as OrganizerHackathonController;
use App\Http\Controllers\Organizer\HackathonStatusController;
use App\Http\Controllers\Organizer\JudgeAssignmentController;
use App\Http\Controllers\Organizer\ScoringCriteriaController;
use App\Http\Controllers\Organizer\SegmentController;
use App\Http\Controllers\Participant\SubmissionController;
use App\Http\Controllers\Participant\TeamController;
use App\Http\Controllers\Participant\TeamInviteController;
use App\Http\Controllers\Participant\TeamMemberController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Guest routes (no auth)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/hackathons', [HackathonController::class, 'publicIndex'])->name('hackathons.index');
Route::get('/h/{slug}', [HackathonController::class, 'publicShow'])->name('hackathons.show');
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

// Auth routes (auth + verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Notification routes (auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
});

// Participant routes
Route::middleware(['auth', 'verified', 'role:participant|super_admin'])->group(function () {
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    Route::get('/hackathons/{hackathon}/teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/hackathons/{hackathon}/teams', [TeamController::class, 'store'])->name('teams.store');
    
    Route::get('/teams/{team}', [TeamController::class, 'show'])->name('teams.show');
    Route::put('/teams/{team}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{team}', [TeamController::class, 'destroy'])->name('teams.destroy');
    
    Route::get('/teams/join/{invite_code}', [TeamInviteController::class, 'show'])->name('teams.join');
    Route::post('/teams/join/{invite_code}', [TeamInviteController::class, 'accept'])->name('teams.join.accept');
    
    Route::post('/teams/{team}/members/{user}/remove', [TeamMemberController::class, 'destroy'])->name('teams.members.remove');
    
    Route::get('/hackathons/{hackathon}/submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/hackathons/{hackathon}/submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::post('/submissions/{submission}/finalize', [SubmissionController::class, 'finalize'])->name('submissions.finalize');
    
    // File uploads
    Route::post('/submissions/{submission}/files', [\App\Http\Controllers\Participant\SubmissionFileController::class, 'store'])->name('submissions.files.store');
    Route::delete('/submission-files/{submissionFile}', [\App\Http\Controllers\Participant\SubmissionFileController::class, 'destroy'])->name('submissions.files.destroy');

    // Announcements
    Route::get('/hackathons/{hackathon}/announcements', [\App\Http\Controllers\Participant\ParticipantAnnouncementController::class, 'index'])->name('participant.announcements.index');
    Route::get('/hackathons/{hackathon}/announcements/{announcement}', [\App\Http\Controllers\Participant\ParticipantAnnouncementController::class, 'show'])->name('participant.announcements.show');
});

// Reopen is organizer action, but view calls submissions.reopen
Route::middleware(['auth', 'verified', 'role:organizer|super_admin'])->group(function () {
    Route::post('/submissions/{submission}/reopen', [SubmissionController::class, 'reopen'])->name('submissions.reopen');
});

// Organizer routes
Route::middleware(['auth', 'verified', 'role:organizer|super_admin'])
    ->prefix('organizer')
    ->name('organizer.')
    ->group(function () {
        Route::resource('hackathons', OrganizerHackathonController::class);
        
        Route::post('hackathons/{hackathon}/status', [HackathonStatusController::class, 'update'])->name('hackathons.status');
        
        Route::post('hackathons/{hackathon}/organizers', [OrganizerHackathonController::class, 'addOrganizer'])->name('hackathons.organizers.store');
        Route::delete('hackathons/{hackathon}/organizers/{user}', [OrganizerHackathonController::class, 'removeOrganizer'])->name('hackathons.organizers.destroy');
        
        Route::resource('hackathons.segments', SegmentController::class)->names('segments')->parameters(['hackathons' => 'hackathon']);
        Route::resource('hackathons.announcements', AnnouncementController::class)->names('announcements')->parameters(['hackathons' => 'hackathon']);
        
        Route::post('hackathons/{hackathon}/announcements/{announcement}/publish', [AnnouncementController::class, 'publish'])->name('announcements.publish');
        
        Route::resource('hackathons.criteria', ScoringCriteriaController::class)->names('criteria')->parameters(['hackathons' => 'hackathon']);
        Route::resource('hackathons.judges', JudgeAssignmentController::class)->names('judges')->parameters(['hackathons' => 'hackathon']);
    });

// Judge routes
Route::middleware(['auth', 'verified', 'role:judge|super_admin'])->group(function () {
    Route::get('/judge/dashboard', [\App\Http\Controllers\Judge\JudgeDashboardController::class, 'index'])->name('judge.dashboard');
    Route::get('/judge/submissions/{submission}/score', [ScoreController::class, 'create'])->name('judge.score.create');
    Route::post('/judge/submissions/{submission}/score', [ScoreController::class, 'store'])->name('judge.score.store');
    Route::put('/judge/submissions/{submission}/score', [ScoreController::class, 'update'])->name('judge.score.update');
    
    Route::get('/hackathons/{hackathon}/leaderboard', [LeaderboardController::class, 'show'])->name('leaderboard.show');
});

// Admin routes
Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        Route::resource('users', AdminUserController::class)->names('users');
        Route::post('users/{user}/restore', [AdminUserController::class, 'restore'])->name('users.restore');
        Route::post('users/{user}/impersonate', [AdminUserController::class, 'impersonate'])->name('users.impersonate');

        
        Route::delete('hackathons/{hackathon}/force', [AdminHackathonController::class, 'forceDelete'])->name('hackathons.force-delete');
        Route::post('hackathons/{hackathon}/restore', [AdminHackathonController::class, 'restore'])->name('hackathons.restore');
        Route::resource('hackathons', AdminHackathonController::class)->names('hackathons');
        
        Route::get('settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [AdminSettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('settings/test-email', [AdminSettingsController::class, 'testEmail'])->name('settings.test-email');
    });

// Impersonation exit must be accessible by any authenticated user who is currently impersonating
Route::get('admin/impersonate/exit', [AdminUserController::class, 'stopImpersonation'])
    ->middleware(['auth'])
    ->name('admin.impersonate.exit');

require __DIR__.'/auth.php';
