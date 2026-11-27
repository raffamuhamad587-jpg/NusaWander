<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use App\Models\DestinationImage;
use App\Models\Trip;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $totalDestinations = Destination::count();
        $activeDestinations = Destination::where('status', 'aktif')->count();
        $inactiveDestinations = Destination::where('status', 'nonaktif')->count();
        $destinationsWithoutPrice = Destination::whereNull('estimasi_harga')->count();
        $destinationsWithoutPhoto = Destination::doesntHave('images')->count();
        $totalPhotos = DestinationImage::count();
        $totalCategories = Category::count();
        $totalTourists = User::where('role', 'wisatawan')->count();
        $totalTrips = Trip::count();

        $recentDestinations = Destination::with(['category', 'images'])
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $topDestinations = Destination::with(['category', 'images'])
            ->withCount('itineraryItems')
            ->orderByDesc('itinerary_items_count')
            ->orderBy('nama')
            ->take(5)
            ->get();

        $categoryBreakdown = Category::withCount('destinations')
            ->orderByDesc('destinations_count')
            ->get(['id', 'nama_kategori']);

        $recentActivities = $recentDestinations->map(fn ($dest) => [
            'title' => 'Destinasi "' . $dest->nama . '" diperbarui',
            'tag' => $dest->category->nama_kategori ?? 'Destinasi',
            'time' => $dest->updated_at,
        ])->values();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalDestinations' => $totalDestinations,
                'activeDestinations' => $activeDestinations,
                'inactiveDestinations' => $inactiveDestinations,
                'destinationsWithoutPrice' => $destinationsWithoutPrice,
                'destinationsWithoutPhoto' => $destinationsWithoutPhoto,
                'totalPhotos' => $totalPhotos,
                'totalCategories' => $totalCategories,
                'totalTourists' => $totalTourists,
                'totalTrips' => $totalTrips,
            ],
            'categoryBreakdown' => $categoryBreakdown,
            'topDestinations' => $topDestinations,
            'recentActivities' => $recentActivities,
        ]);
    }
}
