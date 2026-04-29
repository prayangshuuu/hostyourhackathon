<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\HackathonController;
use App\Http\Controllers\Api\V1\JudgeController;
use App\Http\Controllers\Api\V1\OrganizerController;
use App\Http\Controllers\Api\V1\SubmissionController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    // Auth Routes
    Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });
    });

    // Public Hackathons
    Route::get('/hackathons', [HackathonController::class, 'index']);
    Route::get('/hackathons/{slug}', [HackathonController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        
        // Leaderboard and Announcements
        Route::get('/hackathons/{hackathon}/leaderboard', [HackathonController::class, 'leaderboard']);
        Route::get('/hackathons/{hackathon}/announcements', [AnnouncementController::class, 'index']);
        Route::get('/hackathons/{hackathon}/announcements/{announcement}', [AnnouncementController::class, 'show']);

        // Teams (Participant)
        Route::middleware('role:participant')->group(function () {
            Route::post('/hackathons/{hackathon}/teams', [TeamController::class, 'store']);
            Route::get('/teams/{team}', [TeamController::class, 'show']);
            Route::put('/teams/{team}', [TeamController::class, 'update']);
            Route::post('/teams/join/{invite_code}', [TeamController::class, 'join']);
            Route::delete('/teams/{team}/members/{user}', [TeamController::class, 'removeMember']);

            // Submissions
            Route::post('/hackathons/{hackathon}/submissions', [SubmissionController::class, 'store']);
            Route::get('/submissions/{submission}', [SubmissionController::class, 'show']);
            Route::put('/submissions/{submission}', [SubmissionController::class, 'update']);
            Route::post('/submissions/{submission}/finalize', [SubmissionController::class, 'finalize']);
            Route::post('/submissions/{submission}/files', [SubmissionController::class, 'uploadFile']);
            Route::delete('/submissions/{submission}/files/{file}', [SubmissionController::class, 'deleteFile']);
        });

        // Organizer
        Route::middleware('role_or_permission:super_admin|organizer')->prefix('organizer')->group(function () {
            Route::post('/hackathons', [OrganizerController::class, 'storeHackathon']);
            Route::put('/hackathons/{hackathon}', [OrganizerController::class, 'updateHackathon']);
            // ... other organizer routes mapping omitted for brevity, but they should map to OrganizerController.
        });

        // Judge
        Route::middleware('role:judge')->prefix('judge')->group(function () {
            Route::get('/submissions', [JudgeController::class, 'index']);
            Route::post('/submissions/{submission}/scores', [JudgeController::class, 'storeScores']);
            Route::put('/submissions/{submission}/scores', [JudgeController::class, 'updateScores']);
        });

        // Admin
        Route::middleware('role:super_admin')->prefix('admin')->group(function () {
            Route::get('/users', [AdminController::class, 'users']);
            Route::put('/users/{user}/role', [AdminController::class, 'updateUserRole']);
            Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);
            Route::get('/hackathons', [AdminController::class, 'hackathons']);
            Route::get('/settings', [AdminController::class, 'getSettings']);
            Route::post('/settings', [AdminController::class, 'updateSettings']);
        });
    });
});
