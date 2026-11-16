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
     * Display a listing of the destinations.
     */
    public function index(Request $request): Response
    {
        $query = Destination::with(['category', 'images'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, function ($q, $catId) {
                $q->where('category_id', $catId);
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest();

        $destinations = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);

        return Inertia::render('Admin/Destinations/Index', [
            'destinations' => $destinations,
            'categories' => $categories,
            'filters' => [
                'search' => $request->search ?? '',
                'category_id' => $request->category_id ?? '',
                'status' => $request->status ?? '',
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
        $destination = Destination::create($request->validated());

        // Handle uploaded images if any
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
     * Update the specified destination.
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
        // Delete all images physically
        foreach ($destination->images as $image) {
            $relativePath = str_replace('/storage/', '', $image->url_gambar);
            Storage::disk('public')->delete($relativePath);
        }

        $destination->delete();

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi wisata berhasil dihapus.');
    }
}
