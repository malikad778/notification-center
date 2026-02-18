<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::controller(\App\Http\Controllers\Api\NotificationController::class)->prefix('notifications')->group(function () {
        Route::get('/', 'index');
        Route::patch('/mark-all-read', 'markAllRead');
        Route::get('/preferences', 'getPreferences');
        Route::put('/preferences', 'updatePreferences');
        Route::patch('/{id}/read', 'markAsRead');
    });
});
});
