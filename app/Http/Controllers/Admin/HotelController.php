<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHotelRequest;
use App\Http\Requests\Admin\UpdateHotelRequest;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HotelController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Hotel::query()
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%")
                        ->orWhere('tipe', 'like', "%{$search}%");
                });
            })
            ->when($request->tipe, fn ($q, $tipe) => $q->where('tipe', $tipe))
            ->when($request->kota, fn ($q, $kota) => $q->where('kota', $kota))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy($request->sort ?? 'rating', 'desc');

        $hotels = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Hotels/Index', [
            'hotels' => $hotels,
            'tipes' => Hotel::select('tipe')->distinct()->orderBy('tipe')->pluck('tipe'),
            'kotas' => Hotel::select('kota')->distinct()->orderBy('kota')->pluck('kota'),
            'stats' => [
                'total' => Hotel::count(),
                'aktif' => Hotel::where('status', 'aktif')->count(),
                'menunggu' => Hotel::where('status', 'nonaktif')->count(),
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'tipe' => $request->tipe ?? '',
                'kota' => $request->kota ?? '',
                'status' => $request->status ?? '',
                'sort' => $request->sort ?? 'rating',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Hotels/Create');
    }

    public function store(StoreHotelRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('hotels', 'public');
            $data['image'] = '/storage/' . $path;
        }

        Hotel::create($data);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel berhasil ditambahkan.');
    }

    public function edit(Hotel $hotel): Response
    {
        return Inertia::render('Admin/Hotels/Edit', [
            'hotel' => $hotel,
        ]);
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($hotel->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $hotel->image));
            }
            $path = $request->file('image')->store('hotels', 'public');
            $data['image'] = '/storage/' . $path;
        } else {
            unset($data['image']);
        }

        $hotel->update($data);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel berhasil diperbarui.');
    }

    public function toggleStatus(Hotel $hotel): RedirectResponse
    {
        $newStatus = $hotel->status === 'aktif' ? 'nonaktif' : 'aktif';
        $hotel->update(['status' => $newStatus]);

        return back()->with('success', "Status hotel diubah menjadi {$newStatus}.");
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        if ($hotel->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $hotel->image));
        }

        $hotel->delete();

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel berhasil dihapus.');
    }
}
