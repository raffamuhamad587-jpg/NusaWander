<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use App\Models\DestinationImage;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DestinasiController extends Controller
{
    /** Display the modern landing page. */
    public function home(): Response
    {
        $destinations = Destination::with(['category', 'images'])
            ->where('status', 'aktif')
            ->latest()
            ->take(8)
            ->get();
        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);

        return Inertia::render('Home', [
            'featuredDestinations' => $destinations,
            'categories' => $categories,
        ]);
    }

    /** Display a listing of public destinations. */
    public function index(): Response
    {
        $query = Destination::with(['category', 'images'])
            ->where('status', 'aktif')
            ->latest();

        $destinations = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);

        return Inertia::render('Destinasi/Index', [
            'destinations' => $destinations,
            'categories' => $categories,
            'filters' => [
                'search' => request('search', ''),
                'category_id' => request('category_id', ''),
                'status' => request('status', 'aktif'),
            ],
        ]);
    }

    /** Show destination detail page. */
    public function show(Destination $destination): Response
    {
        $destination->load(['category', 'images', 'reviews.user']);

        if ($destination->status !== 'aktif') {
            abort(404, 'Destinasi tidak tersedia.');
        }

        $relatedDestinations = Destination::where('category_id', $destination->category_id)
            ->where('id', '!=', $destination->id)
            ->where('status', 'aktif')
            ->with('images')
            ->limit(4)
            ->get();

        return Inertia::render('Destinasi/Detail', [
            'destination' => $destination,
            'relatedDestinations' => $relatedDestinations,
            'canReview' => Auth::check() && !$destination->reviews()->where('user_id', Auth::id())->exists(),
        ]);
    }
}