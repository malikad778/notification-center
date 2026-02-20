<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PreferenceController;
use App\Http\Controllers\Api\TemplateController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::controller(NotificationController::class)->prefix('notifications')->group(function () {
        Route::get('/', 'index');
        Route::patch('/mark-all-read', 'markAllRead');
        Route::patch('/{id}/read', 'markAsRead');
        Route::post('/send', 'send');
        Route::post('/bulk', 'sendBulk');
    });

    Route::controller(PreferenceController::class)->prefix('notifications/preferences')->group(function () {
        Route::get('/', 'index');
        Route::put('/', 'update');
    });

    Route::apiResource('templates', TemplateController::class);
});
