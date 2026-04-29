<?php

use App\Http\Controllers\Organizer\HackathonController;
use App\Http\Controllers\Organizer\HackathonStatusController;
use App\Http\Controllers\Organizer\SegmentController;
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

require __DIR__.'/auth.php';

