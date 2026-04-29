<?php

use App\Http\Controllers\Judge\ScoreController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Organizer\HackathonController;
use App\Http\Controllers\Organizer\HackathonStatusController;
use App\Http\Controllers\Organizer\JudgeAssignmentController;
use App\Http\Controllers\Organizer\ScoringCriteriaController;
use App\Http\Controllers\Organizer\SegmentController;
use App\Http\Controllers\Participant\SubmissionController;
use App\Http\Controllers\Participant\SubmissionFileController;
use App\Http\Controllers\Participant\TeamController;
use App\Http\Controllers\Participant\TeamInviteController;
use App\Http\Controllers\Participant\TeamMemberController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
    });

// ── Leaderboard (any authenticated user) ─────────────────────────
Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('hackathons/{hackathon}/leaderboard', [LeaderboardController::class, 'show'])
            ->name('leaderboard.show');
    });

require __DIR__.'/auth.php';
