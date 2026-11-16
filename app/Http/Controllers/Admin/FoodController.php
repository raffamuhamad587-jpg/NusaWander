<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFoodRequest;
use App\Http\Requests\Admin\UpdateFoodRequest;
use App\Models\Food;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FoodController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Food::query()
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('kota', 'like', "%{$search}%")
                        ->orWhere('jenis', 'like', "%{$search}%");
                });
            })
            ->when($request->jenis, fn ($q, $jenis) => $q->where('jenis', $jenis))
            ->when($request->kota, fn ($q, $kota) => $q->where('kota', $kota))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy($request->sort ?? 'rating', 'desc');

        $foods = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Foods/Index', [
            'foods' => $foods,
            'jenes' => Food::select('jenis')->distinct()->orderBy('jenis')->pluck('jenis'),
            'kotas' => Food::select('kota')->distinct()->orderBy('kota')->pluck('kota'),
            'stats' => [
                'total' => Food::count(),
                'aktif' => Food::where('status', 'aktif')->count(),
                'menunggu' => Food::where('status', 'nonaktif')->count(),
            ],
            'filters' => [
                'search' => $request->search ?? '',
                'jenis' => $request->jenis ?? '',
                'kota' => $request->kota ?? '',
                'status' => $request->status ?? '',
                'sort' => $request->sort ?? 'rating',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Foods/Create');
    }

    public function store(StoreFoodRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('foods', 'public');
            $data['image'] = '/storage/' . $path;
        }

        Food::create($data);

        return redirect()->route('admin.foods.index')
            ->with('success', 'Tempat makan berhasil ditambahkan.');
    }

    public function edit(Food $food): Response
    {
        return Inertia::render('Admin/Foods/Edit', [
            'food' => $food,
        ]);
    }

    public function update(UpdateFoodRequest $request, Food $food): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($food->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $food->image));
            }
            $path = $request->file('image')->store('foods', 'public');
            $data['image'] = '/storage/' . $path;
        } else {
            unset($data['image']);
        }

        $food->update($data);

        return redirect()->route('admin.foods.index')
            ->with('success', 'Tempat makan berhasil diperbarui.');
    }

    public function toggleStatus(Food $food): RedirectResponse
    {
        $newStatus = $food->status === 'aktif' ? 'nonaktif' : 'aktif';
        $food->update(['status' => $newStatus]);

        return back()->with('success', "Status diubah menjadi {$newStatus}.");
    }

    public function destroy(Food $food): RedirectResponse
    {
        if ($food->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $food->image));
        }

        $food->delete();

        return redirect()->route('admin.foods.index')
            ->with('success', 'Tempat makan berhasil dihapus.');
    }
}
