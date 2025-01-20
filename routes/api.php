<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ComplaintController;




Route::post('/login', [UserController::class, 'login']);
Route::post('/complaints', [ComplaintController::class, 'store']);
Route::get('/announcements', [AnnouncementController::class, 'index']); // Fetch announcements

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/announcements', [AnnouncementController::class, 'store']); // Create announcement
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


