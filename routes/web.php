<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\Public\DestinasiController::class, 'index'])->name('home');
Route::get('/destinasi', [\App\Http\Controllers\Public\DestinasiController::class, 'index'])->name('destinasi.index');
Route::get('/destinasi/{destination}', [\App\Http\Controllers\Public\DestinasiController::class, 'show'])->name('destinasi.show');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Trips & Itinerary Planner
    Route::get('/trips', [\App\Http\Controllers\TripController::class, 'index'])->name('trips.index');
    Route::get('/trips/create', [\App\Http\Controllers\TripController::class, 'create'])->name('trips.create');
    Route::post('/trips', [\App\Http\Controllers\TripController::class, 'store'])->name('trips.store');
    Route::get('/trips/{trip}', [\App\Http\Controllers\TripController::class, 'show'])->name('trips.show');
    Route::delete('/trips/{trip}', [\App\Http\Controllers\TripController::class, 'destroy'])->name('trips.destroy');

    Route::post('/trips/{trip}/items', [\App\Http\Controllers\TripController::class, 'addItem'])->name('trips.items.add');
    Route::patch('/trips/{trip}/items/reorder', [\App\Http\Controllers\TripController::class, 'reorderItems'])->name('trips.items.reorder');
    Route::delete('/trips/{trip}/items/{item}', [\App\Http\Controllers\TripController::class, 'removeItem'])->name('trips.items.remove');

    Route::post('/trips/{trip}/costs', [\App\Http\Controllers\TripController::class, 'saveCost'])->name('trips.costs.store');
    Route::delete('/trips/{trip}/costs/{cost}', [\App\Http\Controllers\TripController::class, 'deleteCost'])->name('trips.costs.destroy');

    Route::get('/trips/{trip}/export-pdf', [\App\Http\Controllers\TripController::class, 'exportPdf'])->name('trips.export-pdf');
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
