<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
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
        $totalCategories = Category::count();
        $totalTourists = User::where('role', 'wisatawan')->count();

        $recentDestinations = Destination::with('category', 'images')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalDestinations' => $totalDestinations,
                'activeDestinations' => $activeDestinations,
                'inactiveDestinations' => $inactiveDestinations,
                'totalCategories' => $totalCategories,
                'totalTourists' => $totalTourists,
            ],
            'recentDestinations' => $recentDestinations,
        ]);
    }
}
