<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\Admin\DestinationImageController as AdminDestinationImageController;
use App\Http\Controllers\AiDiscoveryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\DestinasiController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

// Public routes (Wisatawan & Guest)
Route::get('/', [DestinasiController::class, 'home'])->name('home');
Route::get('/destinasi', [DestinasiController::class, 'index'])->name('destinasi.index');
Route::get('/destinasi/{destination}', [DestinasiController::class, 'show'])->name('destinasi.show');

// Redirect legacy /dashboard cleanly based on role
Route::get('/dashboard', function () {
    if (request()->user()?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('trips.index');
})->middleware(['auth'])->name('dashboard');

// Authenticated Wisatawan routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Smart Travel & Budget Planner (FR-05 s.d. FR-08)
    Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
    Route::get('/trips/create', [TripController::class, 'create'])->name('trips.create');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show');
    Route::put('/trips/{trip}', [TripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    Route::post('/trips/{trip}/items', [TripController::class, 'addItem'])->name('trips.items.add');
    Route::patch('/trips/{trip}/items/reorder', [TripController::class, 'reorderItems'])->name('trips.items.reorder');
    Route::delete('/trips/{trip}/items/{item}', [TripController::class, 'removeItem'])->name('trips.items.remove');

    Route::post('/trips/{trip}/costs', [TripController::class, 'saveCost'])->name('trips.costs.store');
    Route::delete('/trips/{trip}/costs/{cost}', [TripController::class, 'deleteCost'])->name('trips.costs.destroy');

    Route::get('/trips/{trip}/export-pdf', [TripController::class, 'exportPdf'])->name('trips.export-pdf');

    // AI-Assisted Discovery (FR-13 & FR-14) - throttled external lookup
    Route::post('/ai/alternatives', [AiDiscoveryController::class, 'suggest'])
        ->middleware('throttle:10,1')
        ->name('ai.alternatives');
});

// Admin Portal routes (FR-09 s.d. FR-12)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

    Route::resource('/categories', AdminCategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::patch('/destinations/{destination}/toggle-status', [AdminDestinationController::class, 'toggleStatus'])
        ->name('destinations.toggle-status');

    Route::resource('/destinations', AdminDestinationController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    Route::patch('/destination-images/{destination_image}/order', [AdminDestinationImageController::class, 'updateOrder'])
        ->name('destination-images.order');

    Route::resource('/destination-images', AdminDestinationImageController::class)
        ->only(['store', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
