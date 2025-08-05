<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
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

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Resource Controllers
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::resource('licenses', App\Http\Controllers\Admin\LicenseController::class);
    Route::resource('activations', App\Http\Controllers\Admin\ActivationController::class)->only(['index', 'show', 'destroy']);
    
    // Additional License Actions
    Route::post('/licenses/{license}/block', [App\Http\Controllers\Admin\LicenseController::class, 'block'])->name('licenses.block');
    Route::post('/licenses/{license}/activate', [App\Http\Controllers\Admin\LicenseController::class, 'activate'])->name('licenses.activate');
    Route::post('/licenses/{license}/reset-activations', [App\Http\Controllers\Admin\LicenseController::class, 'resetActivations'])->name('licenses.reset_activations');
    Route::post('/licenses/bulk-action', [App\Http\Controllers\Admin\LicenseController::class, 'bulkAction'])->name('licenses.bulk_action');
    
    // Product Bulk Actions
    Route::post('/products/bulk-action', [App\Http\Controllers\Admin\ProductController::class, 'bulkAction'])->name('products.bulk_action');
    
    // Activation Actions
    Route::post('/activations/{activation}/revoke', [App\Http\Controllers\Admin\ActivationController::class, 'revoke'])->name('activations.revoke');
    Route::post('/activations/{activation}/block-domain', [App\Http\Controllers\Admin\ActivationController::class, 'blockDomain'])->name('activations.block_domain');
    Route::post('/activations/bulk-action', [App\Http\Controllers\Admin\ActivationController::class, 'bulkAction'])->name('activations.bulk_action');
    Route::get('/activations/export', [App\Http\Controllers\Admin\ActivationController::class, 'export'])->name('activations.export');
});

// Admin API routes for external tools
Route::middleware(['auth:sanctum'])->prefix('api/admin')->group(function () {
    Route::post('/reset-activations', [App\Http\Controllers\Admin\LicenseController::class, 'resetActivationsApi']);
});

require __DIR__.'/auth.php';
