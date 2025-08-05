<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LicenseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// License API Routes
Route::prefix('license')->group(function () {
    Route::post('/verify', [LicenseController::class, 'verify']);
    Route::post('/activate', [LicenseController::class, 'activate']);
    Route::post('/deactivate', [LicenseController::class, 'deactivate']);
});

// Additional License API Routes (alternative endpoints) with rate limiting
Route::post('/verify-license', [LicenseController::class, 'verify'])
    ->middleware(['throttle:10,1', 'verify.license.signature']);
Route::post('/activate-license', [LicenseController::class, 'activate'])
    ->middleware('throttle:10,1');
Route::post('/deactivate-license', [LicenseController::class, 'deactivate'])
    ->middleware('throttle:10,1');