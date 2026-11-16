<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');

    Route::resource('/categories', \App\Http\Controllers\Admin\CategoryController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::patch('/destinations/{destination}/toggle-status', [\App\Http\Controllers\Admin\DestinationController::class, 'toggleStatus'])
        ->name('destinations.toggle-status');

    Route::resource('/destinations', \App\Http\Controllers\Admin\DestinationController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::patch('/destination-images/{destination_image}/order', [\App\Http\Controllers\Admin\DestinationImageController::class, 'updateOrder'])
        ->name('destination-images.order');

    Route::resource('/destination-images', \App\Http\Controllers\Admin\DestinationImageController::class)
        ->only(['store', 'destroy']);
});

require __DIR__.'/auth.php';
