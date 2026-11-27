<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Destination;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DestinasiController extends Controller
{
    /** Display the landing page with real catalog data. */
    public function home(): Response
    {
        $destinations = Destination::with(['category', 'images'])
            ->where('status', 'aktif')
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);
        $kotas = Destination::where('status', 'aktif')
            ->select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');

        return Inertia::render('Home', [
            'featuredDestinations' => $destinations,
            'categories' => $categories,
            'kotas' => $kotas,
            'stats' => [
                'totalDestinations' => Destination::where('status', 'aktif')->count(),
                'totalCategories' => Category::count(),
                'totalCities' => $kotas->count(),
            ],
        ]);
    }

    /** Display a searchable & filterable listing of active destinations (FR-02 & FR-03). */
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category_id', '');
        $kota = trim((string) $request->query('kota', ''));

        $query = Destination::with(['category', 'images'])
            ->where('status', 'aktif')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                });
            })
            ->when($categoryId !== '' && is_numeric($categoryId), function ($q) use ($categoryId) {
                $q->where('category_id', (int) $categoryId);
            })
            ->when($kota !== '', function ($q) use ($kota) {
                $q->where('kota', $kota);
            })
            ->latest();

        $destinations = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);
        $kotas = Destination::where('status', 'aktif')
            ->select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');

        return Inertia::render('Destinasi/Index', [
            'destinations' => $destinations,
            'categories' => $categories,
            'kotas' => $kotas,
            'filters' => [
                'search' => $search,
                'category_id' => $categoryId !== '' ? (string) $categoryId : '',
                'kota' => $kota,
            ],
        ]);
    }

    /** Show destination detail page (FR-04). */
    public function show(Destination $destination): Response
    {
        if ($destination->status !== 'aktif') {
            abort(404, 'Destinasi tidak tersedia.');
        }

        $destination->load(['category', 'images']);

        $relatedDestinations = Destination::where('category_id', $destination->category_id)
            ->where('id', '!=', $destination->id)
            ->where('status', 'aktif')
            ->with('images')
            ->limit(4)
            ->get();

        return Inertia::render('Destinasi/Detail', [
            'destination' => $destination,
            'relatedDestinations' => $relatedDestinations,
        ]);
    }
}
