<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTripCostRequest;
use App\Http\Requests\StoreTripItemRequest;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Models\Category;
use App\Models\Destination;
use App\Models\ItineraryItem;
use App\Models\Trip;
use App\Models\TripCost;
use App\Services\BudgetCalculatorService;
use App\Services\ItineraryGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    public function __construct(
        protected ItineraryGeneratorService $generator,
        protected BudgetCalculatorService $budgetCalculator
    ) {}

    /**
     * Display a listing of the authenticated tourist's own trips (FR-08).
     */
    public function index(): Response
    {
        $trips = Trip::withCount(['itineraryItems', 'costs'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(9);

        return Inertia::render('Trips/Index', [
            'trips' => $trips,
        ]);
    }

    /**
     * Show the Smart Travel & Budget Planner creation form (FR-05).
     */
    public function create(Request $request): Response
    {
        $categories = Category::orderBy('nama_kategori')->get(['id', 'nama_kategori']);
        $kotas = Destination::where('status', 'aktif')
            ->select('kota')
            ->distinct()
            ->orderBy('kota')
            ->pluck('kota');

        $selectedDestination = null;
        if ($request->filled('destination_id') && is_numeric($request->destination_id)) {
            $selectedDestination = Destination::where('status', 'aktif')
                ->find((int) $request->destination_id, ['id', 'nama', 'kota', 'category_id']);
        }

        return Inertia::render('Trips/Create', [
            'categories' => $categories,
            'kotas' => $kotas,
            'selectedDestination' => $selectedDestination,
        ]);
    }

    /**
     * Store a newly planned trip and generate rule-based day-by-day itinerary (FR-05, FR-06, FR-07).
     */
    public function store(StoreTripRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $trip = Trip::create([
            'user_id' => Auth::id(),
            'nama_trip' => $validated['nama_trip'],
            'wilayah_tujuan' => $validated['wilayah_tujuan'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'durasi_hari' => (int) $validated['durasi_hari'],
            'jumlah_peserta' => (int) $validated['jumlah_peserta'],
            'anggaran' => $validated['anggaran'],
            'preferensi' => [
                'kategori_ids' => array_values(array_map('intval', $validated['kategori_ids'] ?? [])),
                'prioritas_biaya' => $validated['prioritas_biaya'],
            ],
            'total_estimasi' => 0.00,
        ]);

        // Generate rule-based itinerary from catalog
        $result = $this->generator->generate($trip);

        // Ensure explicitly preselected destination (from detail page) is included on Day 1 if not already generated
        if (! empty($validated['destination_id'])) {
            $alreadyIncluded = $trip->itineraryItems()
                ->where('destination_id', (int) $validated['destination_id'])
                ->exists();

            if (! $alreadyIncluded) {
                $dest = Destination::with('category')->where('status', 'aktif')->find((int) $validated['destination_id']);
                if ($dest) {
                    $maxOrder = (int) ($trip->itineraryItems()->where('hari_ke', 1)->max('urutan_kunjungan') ?? 0);
                    $item = ItineraryItem::create([
                        'trip_id' => $trip->id,
                        'destination_id' => $dest->id,
                        'hari_ke' => 1,
                        'urutan_kunjungan' => $maxOrder + 1,
                        'catatan' => 'Destinasi pilihan utama',
                    ]);
                    $this->budgetCalculator->syncItemCost($trip, $item, $dest);
                    $this->budgetCalculator->recalculateTrip($trip);
                    $result['items_created']++;
                    $result['needs_ai_alternative'] = $result['items_created'] < $trip->durasi_hari;
                }
            }
        }

        $message = $result['items_created'] > 0
            ? "Rencana perjalanan berhasil disusun dengan {$result['items_created']} destinasi dari katalog."
            : 'Rencana perjalanan dibuat, namun belum ditemukan destinasi katalog di wilayah tersebut. Anda dapat menambah manual atau gunakan "Cari alternatif dengan AI".';

        return redirect()->route('trips.show', $trip->id)
            ->with('success', $message);
    }

    /**
     * Display the day-by-day itinerary & budget planner workspace (FR-06, FR-07, FR-08).
     */
    public function show(Trip $trip): Response
    {
        $this->authorizeOwner($trip);

        $trip->load([
            'itineraryItems.destination.category',
            'itineraryItems.destination.images',
            'itineraryItems.costs',
            'costs.itineraryItem.destination',
        ]);

        $availableDestinations = Destination::with('category:id,nama_kategori')
            ->where('status', 'aktif')
            ->orderBy('kota')
            ->orderBy('nama')
            ->get(['id', 'category_id', 'nama', 'kota', 'estimasi_harga', 'jenis_harga']);

        $budgetSummary = $this->budgetCalculator->summarize($trip);
        $needsAiAlternative = $trip->itineraryItems->count() < $trip->durasi_hari;

        return Inertia::render('Trips/Show', [
            'trip' => $trip,
            'budgetSummary' => $budgetSummary,
            'needsAiAlternative' => $needsAiAlternative,
            'availableDestinations' => $availableDestinations,
        ]);
    }

    /**
     * Update trip metadata (budget, duration, participants, start date, area) and recalculate costs.
     */
    public function update(UpdateTripRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        $validated = $request->validated();
        $newDurasi = (int) $validated['durasi_hari'];

        $trip->update([
            'nama_trip' => $validated['nama_trip'],
            'wilayah_tujuan' => $validated['wilayah_tujuan'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'durasi_hari' => $newDurasi,
            'jumlah_peserta' => (int) $validated['jumlah_peserta'],
            'anggaran' => $validated['anggaran'],
        ]);

        // Maintain Business Rule 5.3: hari_ke must always remain valid against durasi_hari
        $trip->itineraryItems()
            ->where('hari_ke', '>', $newDurasi)
            ->update(['hari_ke' => $newDurasi]);

        $this->budgetCalculator->syncCostsWithTripParameters($trip);

        return back()->with('success', 'Detail perjalanan dan kalkulasi anggaran berhasil diperbarui.');
    }

    /**
     * Add a destination item to a specific day in the itinerary.
     */
    public function addItem(StoreTripItemRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        $validated = $request->validated();
        $destination = Destination::with('category')->where('status', 'aktif')->findOrFail($validated['destination_id']);
        $hariKe = (int) $validated['hari_ke'];

        $maxOrder = (int) ($trip->itineraryItems()
            ->where('hari_ke', $hariKe)
            ->max('urutan_kunjungan') ?? 0);

        $item = ItineraryItem::create([
            'trip_id' => $trip->id,
            'destination_id' => $destination->id,
            'hari_ke' => $hariKe,
            'urutan_kunjungan' => $maxOrder + 1,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $this->budgetCalculator->syncItemCost($trip, $item, $destination);
        $this->budgetCalculator->recalculateTrip($trip);

        return back()->with('success', "Destinasi ditambahkan ke Hari {$hariKe}.");
    }

    /**
     * Reorder itinerary items within or across valid trip days.
     */
    public function reorderItems(Request $request, Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        $maxDay = max(1, (int) $trip->durasi_hari);

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:itinerary_items,id'],
            'items.*.hari_ke' => ['required', 'integer', 'min:1', "max:{$maxDay}"],
            'items.*.urutan_kunjungan' => ['required', 'integer', 'min:1'],
        ]);

        foreach ($validated['items'] as $itemData) {
            ItineraryItem::where('id', $itemData['id'])
                ->where('trip_id', $trip->id)
                ->update([
                    'hari_ke' => (int) $itemData['hari_ke'],
                    'urutan_kunjungan' => (int) $itemData['urutan_kunjungan'],
                ]);
        }

        return back()->with('success', 'Urutan jadwal kunjungan berhasil disimpan.');
    }

    /**
     * Remove an item from the itinerary (cascade deletes its linked ticket cost).
     */
    public function removeItem(Trip $trip, ItineraryItem $item): RedirectResponse
    {
        $this->authorizeOwner($trip);

        if ($item->trip_id !== $trip->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $item->delete();
        $this->budgetCalculator->recalculateTrip($trip);

        return back()->with('success', 'Destinasi dihapus dari itinerary.');
    }

    /**
     * Save a trip cost component (supports "belum dihitung" / null subtotal).
     */
    public function saveCost(StoreTripCostRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        $validated = $request->validated();

        $itineraryItemId = $validated['itinerary_item_id'] ?? null;
        if ($itineraryItemId !== null) {
            $belongsToTrip = $trip->itineraryItems()->where('id', $itineraryItemId)->exists();
            if (! $belongsToTrip) {
                abort(403, 'Item itinerary tidak termasuk dalam trip ini.');
            }
        }

        $dasar = $validated['dasar_perhitungan'];
        $jumlah = $this->budgetCalculator->resolveDefaultQuantity($dasar, $trip, $validated['jumlah'] ?? null);

        $isUnknown = ! empty($validated['belum_dihitung']) || ! array_key_exists('harga_satuan', $validated) || $validated['harga_satuan'] === null || $validated['harga_satuan'] === '';
        $hargaSatuan = $isUnknown ? null : (float) $validated['harga_satuan'];
        $subtotal = $this->budgetCalculator->calculateSubtotal($hargaSatuan, $jumlah);

        TripCost::create([
            'trip_id' => $trip->id,
            'itinerary_item_id' => $itineraryItemId,
            'nama_biaya' => $validated['nama_biaya'],
            'jenis_biaya' => $validated['jenis_biaya'],
            'dasar_perhitungan' => $dasar,
            'jumlah' => $jumlah,
            'harga_satuan' => $hargaSatuan,
            'subtotal' => $subtotal,
        ]);

        $this->budgetCalculator->recalculateTrip($trip);

        return back()->with('success', 'Komponen biaya berhasil ditambahkan.');
    }

    /**
     * Delete a cost component and recalculate trip total.
     */
    public function deleteCost(Trip $trip, TripCost $cost): RedirectResponse
    {
        $this->authorizeOwner($trip);

        if ($cost->trip_id !== $trip->id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $cost->delete();
        $this->budgetCalculator->recalculateTrip($trip);

        return back()->with('success', 'Komponen biaya berhasil dihapus.');
    }

    /**
     * Remove the trip.
     */
    public function destroy(Trip $trip): RedirectResponse
    {
        $this->authorizeOwner($trip);

        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Rencana perjalanan berhasil dihapus.');
    }

    /**
     * Export itinerary & budget breakdown to PDF.
     */
    public function exportPdf(Trip $trip): HttpResponse
    {
        $this->authorizeOwner($trip);

        $trip->load(['itineraryItems.destination.category', 'costs', 'user']);
        $budgetSummary = $this->budgetCalculator->summarize($trip);

        $pdf = Pdf::loadView('pdf.trip', [
            'trip' => $trip,
            'budgetSummary' => $budgetSummary,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('NusaWander-Trip-' . $trip->id . '.pdf');
    }

    /**
     * Enforce Business Rule 5.3: tourists can only view or modify their own itineraries.
     */
    protected function authorizeOwner(Trip $trip): void
    {
        if ($trip->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
