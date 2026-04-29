<?php

use App\Http\Controllers\Organizer\HackathonController;
use App\Http\Controllers\Organizer\HackathonStatusController;
use App\Http\Controllers\Organizer\SegmentController;
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
    });

require __DIR__.'/auth.php';

