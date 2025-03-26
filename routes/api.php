<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DialogflowController;

Route::post('/chat', [DialogflowController::class, 'chat']);

Route::post('/login', [UserController::class, 'login']);
Route::post('/complaints', [ComplaintController::class, 'store']);
Route::get('/announcements', [AnnouncementController::class, 'index']); // Fetch announcements
Route::get('/analytics', [AnalyticsController::class, 'getAnalytics']);

Route::middleware('auth:sanctum')->get('/user/profile', [UserController::class, 'profile']);

Route::middleware('auth:sanctum')->get('/user/access-levels', function (Request $request) {
    // Eager load the user's role relationship
    $user = $request->user()->load('userRole');  // Load the user's role with access levels
    
    // Log the role and access_level for debugging
    Log::info('User Role:', ['role' => $user->userRole]);
    Log::info('Access Levels:', ['access_levels' => $user->accessLevels()]);

    return response()->json([
        'access_levels' => $user->accessLevels(),
    ]);
});

Route::middleware('auth:sanctum')->get('/drivers', [UserController::class, 'fetchDrivers']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'getUsers']);
    Route::get('/roles', [UserController::class, 'roles']);

     // New CRUD routes
     Route::post('users', [UserController::class, 'createUser']);  // Create a user
     Route::put('users/{id}', [UserController::class, 'updateUser']);  // Update a user
     Route::put('user/update', [UserController::class, 'updateProfile']);  // Update profile
     Route::delete('users/{id}', [UserController::class, 'deleteUser']);  // Delete a user

    Route::post('/announcements', [AnnouncementController::class, 'store']); // Create announcement
    Route::get('/complaints', [ComplaintController::class, 'index']); // Fetch complaints
    Route::put('/complaints/{id}', [ComplaintController::class, 'update']); // Update complaint (use {id} to specify the complaint)
    Route::post('/complaints/resolve', [ComplaintController::class, 'resolve']);
    // API route to assign concern to a driver
    Route::put('/complaints/{id}/assign', [ComplaintController::class, 'assignToDriver']);

    // New routes for verification and assignment
    Route::get('/complaints/verification', [ComplaintController::class, 'verificationIndex']); // Fetch complaints for verification
    Route::get('/complaints/assignment', [ComplaintController::class, 'assignmentIndex']); // Fetch complaints for assignment
    Route::get('/complaints/routes', [ComplaintController::class, 'assignedRoutes']); // Fetch complaints for assignment
    Route::get('/reports', [AnalyticsController::class, 'getComplaintReport']);

    Route::post('/messages/send', [MessagingController::class, 'sendMessage']);
    Route::get('/messages/inbox', [MessagingController::class, 'inbox']);
    Route::get('/messages/sent', [MessagingController::class, 'sent']);
    Route::get('/messages', [MessagingController::class,'getGroupedMessages']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
