<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDestinationRequest;
use App\Http\Requests\Admin\UpdateDestinationRequest;
use App\Models\Category;
use App\Models\Destination;
use App\Models\DestinationImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DestinationController extends Controller
{
    /**
     * Display a listing of destinations with search, category, status, price, and photo filters.
     */
    public function index(Request $request): Response
    {
        $query = Destination::with(['category', 'images'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->search);
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', (int) $request->category_id);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->query('price_status') === 'missing', function ($q) {
                $q->whereNull('estimasi_harga');
            })
            ->when($request->query('photo_status') === 'missing', function ($q) {
                $q->doesntHave('images');
            })
            ->latest();

        $destinations = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);

        return Inertia::render('Admin/Destinations/Index', [
            'destinations' => $destinations,
            'categories' => $categories,
            'stats' => [
                'total' => Destination::count(),
                'aktif' => Destination::where('status', 'aktif')->count(),
                'menunggu' => Destination::where('status', 'nonaktif')->count(),
                'tanpaHarga' => Destination::whereNull('estimasi_harga')->count(),
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'category_id' => $request->category_id ?? '',
                'status' => $request->status ?? '',
                'price_status' => $request->price_status ?? '',
                'photo_status' => $request->photo_status ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating a new destination.
     */
    public function create(): Response
    {
        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);

        return Inertia::render('Admin/Destinations/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created destination.
     */
    public function store(StoreDestinationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['photos']);

        $destination = Destination::create($data);

        if ($request->hasFile('photos')) {
            $order = 1;
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('destinations', 'public');
                DestinationImage::create([
                    'destination_id' => $destination->id,
                    'url_gambar' => '/storage/' . $path,
                    'urutan_tampil' => $order++,
                ]);
            }
        }

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi wisata berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified destination.
     */
    public function edit(Destination $destination): Response
    {
        $destination->load(['category', 'images']);
        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);

        return Inertia::render('Admin/Destinations/Edit', [
            'destination' => $destination,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified destination (including estimasi_harga & jenis_harga).
     */
    public function update(UpdateDestinationRequest $request, Destination $destination): RedirectResponse
    {
        $destination->update($request->validated());

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi wisata berhasil diperbarui.');
    }

    /**
     * Toggle status between aktif and nonaktif.
     */
    public function toggleStatus(Destination $destination): RedirectResponse
    {
        $newStatus = $destination->status === 'aktif' ? 'nonaktif' : 'aktif';
        $destination->update(['status' => $newStatus]);

        return back()->with('success', "Status destinasi diubah menjadi {$newStatus}.");
    }

    /**
     * Remove the specified destination from storage.
     */
    public function destroy(Destination $destination): RedirectResponse
    {
        if ($destination->itineraryItems()->exists()) {
            return redirect()->route('admin.destinations.index')
                ->with('error', 'Destinasi tidak dapat dihapus karena sedang digunakan pada itinerary wisatawan.');
        }

        foreach ($destination->images as $image) {
            $relativePath = str_replace('/storage/', '', $image->url_gambar);
            Storage::disk('public')->delete($relativePath);
        }

        $destination->delete();

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi wisata berhasil dihapus.');
    }
}
