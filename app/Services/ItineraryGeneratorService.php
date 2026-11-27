<?php

namespace App\Services;

use App\Models\Destination;
use App\Models\ItineraryItem;
use App\Models\Trip;
use Illuminate\Support\Collection;

class ItineraryGeneratorService
{
    public function __construct(
        protected BudgetCalculatorService $budgetCalculator
    ) {}

    /**
     * Generate a rule-based day-by-day itinerary from the internal destination catalog.
     *
     * @return array{items_created: int, matched_count: int, needs_ai_alternative: bool}
     */
    public function generate(Trip $trip): array
    {
        $preferensi = $trip->preferensi ?? [];
        $kategoriIds = array_values(array_filter(array_map('intval', $preferensi['kategori_ids'] ?? [])));
        $prioritasBiaya = $preferensi['prioritas_biaya'] ?? 'seimbang';

        $wilayah = trim($trip->wilayah_tujuan);

        $baseQuery = Destination::with('category')
            ->where('status', 'aktif')
            ->where(function ($q) use ($wilayah) {
                $q->where('kota', 'like', "%{$wilayah}%")
                    ->orWhere('alamat', 'like', "%{$wilayah}%");
            });

        $candidates = (clone $baseQuery)
            ->when(! empty($kategoriIds), fn ($q) => $q->whereIn('category_id', $kategoriIds))
            ->get();

        // If preferred categories yield fewer candidates than trip duration, append other active destinations in the same area
        if (! empty($kategoriIds) && $candidates->count() < $trip->durasi_hari) {
            $existingIds = $candidates->pluck('id')->all();
            $fallbackCandidates = (clone $baseQuery)
                ->whereNotIn('id', $existingIds)
                ->get();
            $candidates = $candidates->concat($fallbackCandidates);
        }

        $ordered = $this->orderCandidates($candidates, $kategoriIds, $prioritasBiaya, $trip);

        // Target up to 3 stops per day, capped by available candidates
        $maxStops = max(1, (int) $trip->durasi_hari) * 3;
        $selected = $this->selectWithinBudget($ordered, $trip, $prioritasBiaya, $maxStops);

        $durasi = max(1, (int) $trip->durasi_hari);
        $dayCounts = array_fill(1, $durasi, 0);
        $itemsCreated = 0;

        foreach ($selected as $index => $destination) {
            $hariKe = ($index % $durasi) + 1;
            $dayCounts[$hariKe]++;

            $item = ItineraryItem::create([
                'trip_id' => $trip->id,
                'destination_id' => $destination->id,
                'hari_ke' => $hariKe,
                'urutan_kunjungan' => $dayCounts[$hariKe],
                'catatan' => $destination->jam_operasional
                    ? 'Jam operasional: ' . $destination->jam_operasional
                    : null,
            ]);

            $this->budgetCalculator->syncItemCost($trip, $item, $destination);
            $itemsCreated++;
        }

        $this->budgetCalculator->recalculateTrip($trip);

        return [
            'items_created' => $itemsCreated,
            'matched_count' => $candidates->count(),
            'needs_ai_alternative' => $itemsCreated < $durasi,
        ];
    }

    protected function orderCandidates(Collection $candidates, array $kategoriIds, string $prioritasBiaya, Trip $trip): Collection
    {
        return $candidates->sortBy(function (Destination $dest) use ($kategoriIds, $prioritasBiaya, $trip) {
            $categoryRank = (! empty($kategoriIds) && in_array($dest->category_id, $kategoriIds, true)) ? 0 : 1;
            $estimatedCost = $this->estimateDestinationCost($dest, $trip);

            $priceRank = match ($prioritasBiaya) {
                'hemat' => $estimatedCost ?? PHP_FLOAT_MAX,
                'nyaman' => $estimatedCost !== null ? -$estimatedCost : 0,
                default => $estimatedCost ?? 1_000_000_000,
            };

            return [$categoryRank, $priceRank, $dest->id];
        })->values();
    }

    protected function selectWithinBudget(Collection $ordered, Trip $trip, string $prioritasBiaya, int $maxStops): Collection
    {
        if ($ordered->isEmpty()) {
            return collect();
        }

        $selected = collect();
        $runningTotal = 0.0;
        $budgetCap = (float) $trip->anggaran;
        $minNeeded = min($ordered->count(), max(1, (int) $trip->durasi_hari));

        foreach ($ordered as $dest) {
            if ($selected->count() >= $maxStops) {
                break;
            }

            $cost = $this->estimateDestinationCost($dest, $trip) ?? 0.0;

            // For 'hemat' mode, respect budget once we have at least 1 stop per day (or if budget > 0)
            if ($prioritasBiaya === 'hemat' && $budgetCap > 0 && $selected->count() >= $minNeeded && ($runningTotal + $cost) > $budgetCap) {
                continue;
            }

            $selected->push($dest);
            $runningTotal += $cost;
        }

        return $selected;
    }

    protected function estimateDestinationCost(Destination $dest, Trip $trip): ?float
    {
        if ($dest->estimasi_harga === null) {
            return null;
        }

        $multiplier = $dest->jenis_harga === 'per_kelompok' ? 1 : max(1, (int) $trip->jumlah_peserta);

        return (float) $dest->estimasi_harga * $multiplier;
    }
}
