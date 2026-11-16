<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\TripCost;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    /**
     * Display a listing of user's trips.
     */
    public function index(): Response
    {
        $trips = Trip::with(['destination', 'itineraryItems'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(9);

        return Inertia::render('Trips/Index', [
            'trips' => $trips,
        ]);
    }

    /**
     * Show the form for creating a new trip.
     */
    public function create(Request $request): Response
    {
        $destinations = Destination::where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kota', 'estimasi_harga', 'jenis_harga']);

        return Inertia::render('Trips/Create', [
            'destinations' => $destinations,
            'selectedDestinationId' => $request->destination_id ? (int) $request->destination_id : null,
        ]);
    }

    /**
     * Store a newly created trip.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'destination_id' => 'required|exists:destinations,id',
            'destination_ids' => 'nullable|array',
            'destination_ids.*' => 'exists:destinations,id',
        ], [
            'judul.required' => 'Judul rencana perjalanan wajib diisi.',
            'destination_id.required' => 'Destinasi utama wajib dipilih.',
        ]);

        $mainDest = Destination::findOrFail($validated['destination_id']);

        $trip = Trip::create([
            'user_id' => Auth::id(),
            'destination_id' => $mainDest->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'total_estimasi_harga' => $mainDest->estimasi_harga,
            'status' => 'draft',
        ]);

        // Add main destination as first itinerary item
        ItineraryItem::create([
            'trip_id' => $trip->id,
            'destination_id' => $mainDest->id,
            'urutan' => 1,
            'judul' => $mainDest->nama,
            'catatan' => 'Destinasi Utama',
            'estimasi_harga' => $mainDest->estimasi_harga,
        ]);

        // Add optional extra destinations
        if (!empty($validated['destination_ids'])) {
            $order = 2;
            foreach ($validated['destination_ids'] as $destId) {
                if ($destId != $mainDest->id) {
                    $extra = Destination::find($destId);
                    if ($extra) {
                        ItineraryItem::create([
                            'trip_id' => $trip->id,
                            'destination_id' => $extra->id,
                            'urutan' => $order++,
                            'judul' => $extra->nama,
                            'catatan' => null,
                            'estimasi_harga' => $extra->estimasi_harga,
                        ]);
                    }
                }
            }
        }

        $this->recalculateTripTotal($trip);

        return redirect()->route('trips.show', $trip->id)
            ->with('success', 'Rencana perjalanan berhasil dibuat.');
    }

    /**
     * Display the trip planner workspace.
     */
    public function show(Trip $trip): Response
    {
        $this->authorizeUser($trip);

        $trip->load(['destination', 'itineraryItems.destination.images', 'costs']);

        $availableDestinations = Destination::where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kota', 'estimasi_harga', 'jenis_harga']);

        return Inertia::render('Trips/Show', [
            'trip' => $trip,
            'availableDestinations' => $availableDestinations,
        ]);
    }

    /**
     * Add an item to the itinerary.
     */
    public function addItem(Request $request, Trip $trip): RedirectResponse
    {
        $this->authorizeUser($trip);

        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'catatan' => 'nullable|string|max:500',
        ]);

        $destination = Destination::findOrFail($validated['destination_id']);
        $maxOrder = $trip->itineraryItems()->max('urutan') ?? 0;

        ItineraryItem::create([
            'trip_id' => $trip->id,
            'destination_id' => $destination->id,
            'urutan' => $maxOrder + 1,
            'judul' => $destination->nama,
            'catatan' => $validated['catatan'] ?? null,
            'estimasi_harga' => $destination->estimasi_harga,
        ]);

        $this->recalculateTripTotal($trip);

        return back()->with('success', 'Destinasi ditambahkan ke itinerary.');
    }

    /**
     * Reorder itinerary items (Drag and drop).
     */
    public function reorderItems(Request $request, Trip $trip): RedirectResponse
    {
        $this->authorizeUser($trip);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:itinerary_items,id',
            'items.*.urutan' => 'required|integer',
        ]);

        foreach ($validated['items'] as $itemData) {
            ItineraryItem::where('id', $itemData['id'])
                ->where('trip_id', $trip->id)
                ->update(['urutan' => $itemData['urutan']]);
        }

        return back()->with('success', 'Urutan jadwal berhasil disimpan.');
    }

    /**
     * Remove an item from the itinerary.
     */
    public function removeItem(Trip $trip, ItineraryItem $item): RedirectResponse
    {
        $this->authorizeUser($trip);

        if ($item->trip_id !== $trip->id) {
            abort(403);
        }

        $item->delete();
        $this->recalculateTripTotal($trip);

        return back()->with('success', 'Item berhasil dihapus dari itinerary.');
    }

    /**
     * Save custom trip costs (budget breakdown).
     */
    public function saveCost(Request $request, Trip $trip): RedirectResponse
    {
        $this->authorizeUser($trip);

        $validated = $request->validate([
            'harga_per_orang' => 'required|numeric|min:0',
            'harga_per_kelompok' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255',
        ]);

        TripCost::create([
            'trip_id' => $trip->id,
            'harga_per_orang' => $validated['harga_per_orang'],
            'harga_per_kelompok' => $validated['harga_per_kelompok'],
            'keterangan' => $validated['keterangan'] ?? 'Estimasi Tambahan',
        ]);

        $this->recalculateTripTotal($trip);

        return back()->with('success', 'Rincian biaya berhasil ditambahkan.');
    }

    /**
     * Delete a cost item.
     */
    public function deleteCost(Trip $trip, TripCost $cost): RedirectResponse
    {
        $this->authorizeUser($trip);

        if ($cost->trip_id !== $trip->id) {
            abort(403);
        }

        $cost->delete();
        $this->recalculateTripTotal($trip);

        return back()->with('success', 'Biaya berhasil dihapus.');
    }

    /**
     * Remove the trip.
     */
    public function destroy(Trip $trip): RedirectResponse
    {
        $this->authorizeUser($trip);

        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Rencana perjalanan berhasil dihapus.');
    }

    /**
     * Export itinerary to PDF.
     */
    public function exportPdf(Trip $trip): HttpResponse
    {
        $this->authorizeUser($trip);

        $trip->load(['destination', 'itineraryItems.destination', 'costs', 'user']);

        $pdf = Pdf::loadView('pdf.trip', [
            'trip' => $trip,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('NusaWander-Trip-' . $trip->id . '.pdf');
    }

    /**
     * Helper to recalculate total estimasi harga.
     */
    protected function recalculateTripTotal(Trip $trip): void
    {
        $itemsTotal = (float) $trip->itineraryItems()->sum('estimasi_harga');
        $costsTotal = (float) $trip->costs()->sum('harga_per_orang') + (float) $trip->costs()->sum('harga_per_kelompok');

        $trip->update([
            'total_estimasi_harga' => $itemsTotal + $costsTotal,
        ]);
    }

    protected function authorizeUser(Trip $trip): void
    {
        if ($trip->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}